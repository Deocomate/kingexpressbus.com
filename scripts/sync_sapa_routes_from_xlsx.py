from __future__ import annotations

import json
import math
import re
import unicodedata
from datetime import timedelta
from dataclasses import dataclass, field
from pathlib import Path

import pandas as pd
import pymysql


@dataclass
class RouteData:
    start_key: str
    end_key: str
    pickups: list[str] = field(default_factory=list)
    dropoffs: list[str] = field(default_factory=list)
    notes: list[str] = field(default_factory=list)
    schedules: list[tuple[str, str]] = field(default_factory=list)
    prices: dict[str, int | None] = field(default_factory=lambda: {"sleeper": None, "single": None, "double": None})


def fold_text(value: str) -> str:
    text = unicodedata.normalize("NFKD", value)
    text = text.replace("\u0111", "d").replace("\u0110", "D")
    text = "".join(ch for ch in text if not unicodedata.combining(ch))
    return re.sub(r"\s+", " ", text).strip().lower()


def place_key(raw: str) -> str | None:
    t = fold_text(raw)
    if "ninh binh" in t or "tam coc" in t:
        return "ninh_binh"
    if "ha noi" in t:
        return "ha_noi"
    if "sapa" in t:
        return "sapa"
    if "phong nha" in t:
        return "phong_nha"
    if re.search(r"\bhue\b", t):
        return "hue"
    if "da nang" in t:
        return "da_nang"
    if "hoi an" in t:
        return "hoi_an"
    return None


def parse_time(value) -> str | None:
    if pd.isna(value):
        return None
    if hasattr(value, "strftime"):
        return value.strftime("%H:%M:%S")
    text = str(value).strip()
    m = re.match(r"^(\d{1,2}):(\d{2})(?::(\d{2}))?$", text)
    if not m:
        return None
    return f"{int(m.group(1)):02d}:{int(m.group(2)):02d}:{int(m.group(3) or 0):02d}"


def normalize_db_time(value) -> str:
    if isinstance(value, timedelta):
        total = int(value.total_seconds()) % (24 * 3600)
        h = total // 3600
        m = (total % 3600) // 60
        s = total % 60
        return f"{h:02d}:{m:02d}:{s:02d}"
    text = str(value)
    m = re.match(r"^(\d{1,2}):(\d{2})(?::(\d{2}))?$", text)
    if m:
        return f"{int(m.group(1)):02d}:{int(m.group(2)):02d}:{int(m.group(3) or 0):02d}"
    return text


def parse_price(value) -> int | None:
    if pd.isna(value):
        return None
    try:
        number = float(str(value).replace(",", "").strip())
    except Exception:
        return None
    if math.isnan(number):
        return None
    val = int(round(number))
    if val <= 0:
        return None
    return val * 1000 if val < 10000 else val


def dedupe_keep_order(values: list[str]) -> list[str]:
    out: list[str] = []
    seen: set[str] = set()
    for value in values:
        key = fold_text(value)
        if not key or key in seen:
            continue
        seen.add(key)
        out.append(value.strip())
    return out


def load_routes(xlsx_path: Path) -> list[RouteData]:
    df = pd.read_excel(xlsx_path, sheet_name=0)
    routes: list[RouteData] = []
    current: RouteData | None = None

    for idx, row in df.iterrows():
        if idx < 11:
            continue
        route_cell = "" if pd.isna(row.iloc[0]) else str(row.iloc[0]).strip()
        if fold_text(route_cell).startswith("luu y"):
            break

        if route_cell:
            parts = [p.strip() for p in re.split(r"\s*-\s*", route_cell) if p.strip()]
            if len(parts) >= 2:
                start_key = place_key(parts[0])
                end_key = place_key(parts[1])
                if start_key and end_key:
                    current = RouteData(start_key=start_key, end_key=end_key)
                    routes.append(current)
                else:
                    current = None
            else:
                current = None
        if not current:
            continue

        pickup = "" if pd.isna(row.iloc[1]) else str(row.iloc[1]).strip()
        dropoff = "" if pd.isna(row.iloc[3]) else str(row.iloc[3]).strip()
        note = "" if pd.isna(row.iloc[8]) else str(row.iloc[8]).strip()
        if pickup:
            current.pickups.append(pickup)
        if dropoff:
            current.dropoffs.append(dropoff)
        if note and not fold_text(note).startswith("voi tuyen"):
            current.notes.append(note)

        start = parse_time(row.iloc[2])
        end = parse_time(row.iloc[4])
        if start and end:
            current.schedules.append((start, end))

        sell = [parse_price(row.iloc[9]), parse_price(row.iloc[10]), parse_price(row.iloc[11])]
        ta = [parse_price(row.iloc[5]), parse_price(row.iloc[6]), parse_price(row.iloc[7])]
        for i, key in enumerate(("sleeper", "single", "double")):
            if current.prices[key] is None and sell[i] is not None:
                current.prices[key] = sell[i]
            elif current.prices[key] is None and ta[i] is not None:
                current.prices[key] = ta[i]

    for r in routes:
        r.pickups = dedupe_keep_order(r.pickups)
        r.dropoffs = dedupe_keep_order(r.dropoffs)
        r.notes = dedupe_keep_order(r.notes)
        r.schedules = list(dict.fromkeys(r.schedules))

    by_pair = {(r.start_key, r.end_key): r for r in routes}
    for r in routes:
        reverse = by_pair.get((r.end_key, r.start_key))
        if reverse:
            for key in ("sleeper", "single", "double"):
                if r.prices[key] is None:
                    r.prices[key] = reverse.prices[key]
    return routes


def load_env(path: Path) -> dict[str, str]:
    out: dict[str, str] = {}
    for line in path.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        k, v = line.split("=", 1)
        out[k.strip()] = v.strip().strip('"').strip("'")
    return out


def slugify(text: str) -> str:
    return re.sub(r"-+", "-", re.sub(r"[^a-z0-9]+", "-", fold_text(text))).strip("-")


def infer_note_province(note: str) -> str | None:
    k = place_key(note)
    if k:
        return k
    t = fold_text(note)
    if any(x in t for x in ("dong anh", "soc son", "noi bai", "cau giay", "bac tu liem", "hoan kiem", "tran quang khai", "vo van kiet", "kim chung")):
        return "ha_noi"
    if any(x in t for x in ("lao cai", "dien bien phu", "thach son", "coc san")):
        return "sapa"
    return None


def compute_duration_label(schedules: list[tuple[str, str]]) -> str | None:
    if not schedules:
        return None
    mins: list[int] = []
    for start, end in schedules:
        sh, sm, _ = [int(x) for x in start.split(":")]
        eh, em, _ = [int(x) for x in end.split(":")]
        start_min = sh * 60 + sm
        end_min = eh * 60 + em
        if end_min < start_min:
            end_min += 24 * 60
        duration = end_min - start_min
        if duration < 4 * 60:
            duration += 24 * 60
        mins.append(duration)
    lo = min(mins)
    hi = max(mins)
    if lo == hi:
        h, m = divmod(lo, 60)
        return f"{h}h" if m == 0 else f"{h}h {m}m"
    l_h, l_m = divmod(lo, 60)
    h_h, h_m = divmod(hi, 60)
    lo_text = f"{l_h}h" if l_m == 0 else f"{l_h}h {l_m}m"
    hi_text = f"{h_h}h" if h_m == 0 else f"{h_h}h {h_m}m"
    return f"{lo_text} - {hi_text}"


def fmt_price(price: int | None) -> str:
    return "N/A" if price is None else f"{price:,} VND".replace(",", ".")


def build_route_content(route_name: str, route: RouteData) -> str:
    schedules = ", ".join([f"{s} - {e}" for s, e in route.schedules]) if route.schedules else "Liên hệ"
    pickups = "; ".join(route.pickups[:3]) if route.pickups else "Liên hệ"
    dropoffs = "; ".join(route.dropoffs[:3]) if route.dropoffs else "Liên hệ"
    return (
        f"<p><strong>{route_name}</strong></p>"
        f"<p>Lịch chạy: {schedules}</p>"
        f"<p>Điểm đón: {pickups}</p>"
        f"<p>Điểm trả: {dropoffs}</p>"
        f"<p>Giá: Giường nằm {fmt_price(route.prices.get('sleeper'))}, "
        f"Cabin đơn {fmt_price(route.prices.get('single'))}, "
        f"Cabin đôi {fmt_price(route.prices.get('double'))}</p>"
    )


def main() -> None:
    repo = Path(__file__).resolve().parents[1]
    xlsx = sorted((repo / "docs" / "data").glob("*.xlsx"))[0]
    env = load_env(repo / ".env")
    routes = load_routes(xlsx)
    if len(routes) != 12:
        raise RuntimeError(f"Expected 12 routes, found {len(routes)}")

    conn = pymysql.connect(
        host=env.get("DB_HOST", "127.0.0.1"),
        port=int(env.get("DB_PORT", "3306")),
        user=env.get("DB_USERNAME", "root"),
        password=env.get("DB_PASSWORD", ""),
        database=env["DB_DATABASE"],
        charset="utf8mb4",
        autocommit=False,
        cursorclass=pymysql.cursors.DictCursor,
    )

    province_name_by_key: dict[str, str] = {}
    route_ids: list[int] = []
    route_menu_items: list[tuple[int, str, str, int]] = []
    created_routes = updated_routes = created_stops = created_trips = updated_trips = deleted_trips = 0
    created_menus = updated_menus = deleted_menus = 0

    image_file_by_key = {
        "ha_noi": "ha-noi.jpg",
        "sapa": "sapa.jpg",
        "ninh_binh": "ninh-binh.jpg",
        "phong_nha": "phong-nha.jpg",
        "hue": "hue.jpg",
        "da_nang": "da-nang.jpg",
        "hoi_an": "hoi-an.jpg",
    }
    distance_by_pair = {
        ("ha_noi", "sapa"): 315,
        ("sapa", "ha_noi"): 315,
        ("sapa", "ninh_binh"): 390,
        ("ninh_binh", "sapa"): 390,
        ("sapa", "phong_nha"): 620,
        ("phong_nha", "sapa"): 620,
        ("sapa", "hue"): 700,
        ("hue", "sapa"): 700,
        ("sapa", "da_nang"): 790,
        ("da_nang", "sapa"): 790,
        ("sapa", "hoi_an"): 820,
        ("hoi_an", "sapa"): 820,
    }

    try:
        with conn.cursor() as cur:
            cur.execute("SELECT id, name FROM provinces")
            for row in cur.fetchall():
                key = place_key(row["name"])
                if key:
                    province_name_by_key[key] = row["name"]
            cur.execute("SELECT id, province_id FROM districts ORDER BY id")
            district_by_province: dict[int, int] = {}
            for row in cur.fetchall():
                district_by_province.setdefault(row["province_id"], row["id"])
            cur.execute("SELECT id, name, model_name FROM buses ORDER BY id")
            buses = cur.fetchall()
            bus_id_by_type = {
                "sleeper": next(b["id"] for b in buses if fold_text(b["name"]) == "sleeper"),
                "single": next(b["id"] for b in buses if "cabin single" in fold_text(b["name"]) and "vip" not in fold_text(b["name"])),
                "double": next(b["id"] for b in buses if "cabin double" in fold_text(b["name"]) and "vip" not in fold_text(b["name"])),
            }

            cur.execute("SELECT s.id, s.address, d.province_id FROM stops s JOIN districts d ON d.id=s.district_id")
            stop_by_key = {(r["province_id"], fold_text(r["address"])): r["id"] for r in cur.fetchall()}

            for index, route in enumerate(routes):
                start_name = province_name_by_key[route.start_key]
                end_name = province_name_by_key[route.end_key]
                route_name = f"{start_name} - {end_name}"
                route_slug = slugify(route_name)
                route_title = f"Vé xe {route_name}"
                route_description = f"Đặt vé xe giường nằm, cabin đơn, cabin đôi chất lượng cao tuyến {route_name}"
                route_duration = compute_duration_label(route.schedules)
                route_distance = distance_by_pair.get((route.start_key, route.end_key))
                route_thumbnail = f"/client/images/city_imgs/{image_file_by_key[route.end_key]}"
                route_images = json.dumps([route_thumbnail], ensure_ascii=False)
                route_content = build_route_content(route_name, route)
                cur.execute("SELECT id FROM provinces WHERE name=%s", (start_name,))
                start_id = cur.fetchone()["id"]
                cur.execute("SELECT id FROM provinces WHERE name=%s", (end_name,))
                end_id = cur.fetchone()["id"]
                hotel_pickup = 1 if {route.start_key, route.end_key} == {"ha_noi", "sapa"} else 0
                cur.execute("SELECT id FROM routes WHERE slug=%s", (route_slug,))
                existing = cur.fetchone()
                if existing:
                    route_id = existing["id"]
                    updated_routes += 1
                    cur.execute(
                        "UPDATE routes SET province_start_id=%s, province_end_id=%s, name=%s, title=%s, description=%s, duration=%s, distance_km=%s, price_default=%s, thumbnail_url=%s, image_list_url=%s, content=%s, available_hotel_pickup=%s, priority=%s, updated_at=NOW() WHERE id=%s",
                        (
                            start_id,
                            end_id,
                            route_name,
                            route_title,
                            route_description,
                            route_duration,
                            route_distance,
                            route.prices["sleeper"] or 0,
                            route_thumbnail,
                            route_images,
                            route_content,
                            hotel_pickup,
                            100 - index,
                            route_id,
                        ),
                    )
                else:
                    created_routes += 1
                    cur.execute(
                        "INSERT INTO routes (province_start_id, province_end_id, name, slug, title, description, duration, distance_km, price_default, thumbnail_url, image_list_url, content, available_hotel_pickup, priority, created_at, updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,NOW(),NOW())",
                        (
                            start_id,
                            end_id,
                            route_name,
                            route_slug,
                            route_title,
                            route_description,
                            route_duration,
                            route_distance,
                            route.prices["sleeper"] or 0,
                            route_thumbnail,
                            route_images,
                            route_content,
                            hotel_pickup,
                            100 - index,
                        ),
                    )
                    route_id = cur.lastrowid
                route_ids.append(route_id)
                route_menu_items.append((route_id, route_name, route_slug, index))

                route_stop_items: list[tuple[int, str, str]] = []
                for address in route.pickups:
                    route_stop_items.append((start_id, address, "pickup"))
                for address in route.dropoffs:
                    route_stop_items.append((end_id, address, "dropoff"))
                for note in route.notes:
                    province_key = infer_note_province(note)
                    if province_key == route.start_key:
                        route_stop_items.append((start_id, note, "pickup"))
                    elif province_key == route.end_key:
                        route_stop_items.append((end_id, note, "dropoff"))
                    else:
                        route_stop_items.append((start_id, note, "both"))

                inserted_route_stops: list[tuple[int, str]] = []
                stop_rows: list[tuple[int, int, str, int]] = []
                for province_id, address, stop_type in route_stop_items:
                    stop_key = (province_id, fold_text(address))
                    stop_id = stop_by_key.get(stop_key)
                    if stop_id is None:
                        created_stops += 1
                        cur.execute(
                            "INSERT INTO stops (district_id, name, address, priority, created_at, updated_at) VALUES (%s,%s,%s,0,NOW(),NOW())",
                            (district_by_province[province_id], address, address),
                        )
                        stop_id = cur.lastrowid
                        stop_by_key[stop_key] = stop_id
                    marker = (stop_id, stop_type)
                    if marker in inserted_route_stops:
                        continue
                    inserted_route_stops.append(marker)
                    stop_rows.append((route_id, stop_id, stop_type, len(stop_rows)))

                cur.execute("DELETE FROM route_stops WHERE route_id=%s", (route_id,))
                if stop_rows:
                    cur.executemany(
                        "INSERT INTO route_stops (route_id, stop_id, stop_type, priority, created_at, updated_at) VALUES (%s,%s,%s,%s,NOW(),NOW())",
                        stop_rows,
                    )

                cur.execute("SELECT id, bus_id, start_time, end_time FROM trips WHERE route_id=%s", (route_id,))
                existing_trips = {
                    (r["bus_id"], normalize_db_time(r["start_time"]), normalize_db_time(r["end_time"])): r["id"]
                    for r in cur.fetchall()
                }
                desired: set[tuple[int, str, str]] = set()
                for start_time, end_time in route.schedules:
                    for bus_type in ("sleeper", "single", "double"):
                        price = route.prices[bus_type]
                        if price is None:
                            continue
                        bus_id = bus_id_by_type[bus_type]
                        key = (bus_id, start_time, end_time)
                        desired.add(key)
                        if key in existing_trips:
                            updated_trips += 1
                            cur.execute("UPDATE trips SET price=%s, is_active=1, updated_at=NOW() WHERE id=%s", (price, existing_trips[key]))
                        else:
                            created_trips += 1
                            cur.execute(
                                "INSERT INTO trips (bus_id, route_id, start_time, end_time, price, is_active, priority, created_at, updated_at) VALUES (%s,%s,%s,%s,%s,1,0,NOW(),NOW())",
                                (bus_id, route_id, start_time, end_time, price),
                            )

                for key, trip_id in existing_trips.items():
                    if key not in desired:
                        deleted_trips += 1
                        cur.execute("DELETE FROM trips WHERE id=%s", (trip_id,))

            cur.execute("SELECT id FROM routes")
            existing_route_ids = {r["id"] for r in cur.fetchall()}
            for delete_id in sorted(existing_route_ids - set(route_ids)):
                cur.execute("DELETE FROM routes WHERE id=%s", (delete_id,))

            cur.execute("SELECT id FROM menus WHERE parent_id IS NULL AND name=%s LIMIT 1", ("Tuyến đường",))
            parent_menu = cur.fetchone()
            if parent_menu:
                parent_id = parent_menu["id"]
            else:
                cur.execute(
                    "INSERT INTO menus (name, url, parent_id, priority, type, related_id, created_at, updated_at) VALUES (%s,%s,NULL,%s,%s,NULL,NOW(),NOW())",
                    ("Tuyến đường", "#", 1, "custom_link"),
                )
                parent_id = cur.lastrowid
            cur.execute("SELECT id, related_id FROM menus WHERE parent_id=%s AND type='route'", (parent_id,))
            existing_route_menus = cur.fetchall()
            existing_menu_by_route = {row["related_id"]: row["id"] for row in existing_route_menus if row["related_id"] is not None}
            desired_route_ids = {r[0] for r in route_menu_items}
            for route_id, route_name, route_slug, idx in route_menu_items:
                url = f"/tuyen-duong/{route_slug}"
                if route_id in existing_menu_by_route:
                    updated_menus += 1
                    cur.execute(
                        "UPDATE menus SET name=%s, url=%s, priority=%s, related_id=%s, updated_at=NOW() WHERE id=%s",
                        (route_name, url, idx, route_id, existing_menu_by_route[route_id]),
                    )
                else:
                    created_menus += 1
                    cur.execute(
                        "INSERT INTO menus (name, url, parent_id, priority, type, related_id, created_at, updated_at) VALUES (%s,%s,%s,%s,'route',%s,NOW(),NOW())",
                        (route_name, url, parent_id, idx, route_id),
                    )
            for row in existing_route_menus:
                if row["related_id"] not in desired_route_ids:
                    deleted_menus += 1
                    cur.execute("DELETE FROM menus WHERE id=%s", (row["id"],))

            cur.execute(
                "DELETE s FROM stops s LEFT JOIN route_stops rs ON rs.stop_id=s.id LEFT JOIN bookings b1 ON b1.pickup_stop_id=s.id LEFT JOIN bookings b2 ON b2.dropoff_stop_id=s.id WHERE rs.id IS NULL AND b1.id IS NULL AND b2.id IS NULL"
            )
            deleted_stops = cur.rowcount

        conn.commit()
    finally:
        conn.close()

    print("SYNC_DONE")
    print(f"routes_created={created_routes} routes_updated={updated_routes} routes_kept={len(route_ids)}")
    print(f"trips_created={created_trips} trips_updated={updated_trips} trips_deleted={deleted_trips}")
    print(f"menus_created={created_menus} menus_updated={updated_menus} menus_deleted={deleted_menus}")
    print(f"stops_created={created_stops} stops_deleted={deleted_stops}")


if __name__ == "__main__":
    main()
