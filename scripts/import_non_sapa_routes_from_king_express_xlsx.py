from __future__ import annotations

import json
import re
import unicodedata
from dataclasses import dataclass, field
from datetime import timedelta
from pathlib import Path

import pandas as pd
import pymysql


@dataclass
class RouteAggregate:
    start_key: str
    end_key: str
    pickups: list[str] = field(default_factory=list)
    dropoffs: list[str] = field(default_factory=list)
    durations: list[int] = field(default_factory=list)
    prices: list[int] = field(default_factory=list)
    trips: dict[tuple[str, str, str], int] = field(default_factory=dict)


def fold_text(value: str) -> str:
    text = unicodedata.normalize("NFKD", value or "")
    text = text.replace("\u0111", "d").replace("\u0110", "D")
    text = "".join(ch for ch in text if not unicodedata.combining(ch))
    return re.sub(r"\s+", " ", text).strip().lower()


def slugify(text: str) -> str:
    return re.sub(r"-+", "-", re.sub(r"[^a-z0-9]+", "-", fold_text(text))).strip("-")


def parse_price(value) -> int | None:
    if pd.isna(value):
        return None
    raw = str(value).strip()
    m = re.findall(r"\d+(?:\.\d+)?", raw)
    if not m:
        return None
    try:
        num = float(m[0])
    except Exception:
        return None
    return int(round(num))


def parse_duration_minutes(text: str) -> int | None:
    t = fold_text(text)
    if not t:
        return None
    m_h = re.search(r"(\d+)\s*h", t)
    m_m = re.search(r"(\d+)\s*m", t)
    if not m_h and not m_m:
        return None
    h = int(m_h.group(1)) if m_h else 0
    m = int(m_m.group(1)) if m_m else 0
    return h * 60 + m


def parse_departures(text: str) -> list[str]:
    if pd.isna(text):
        return []
    out: list[str] = []
    for part in str(text).split(","):
        part = part.strip()
        m = re.match(r"^(\d{1,2}):(\d{2})$", part)
        if not m:
            continue
        out.append(f"{int(m.group(1)):02d}:{int(m.group(2)):02d}:00")
    return list(dict.fromkeys(out))


def add_minutes(hhmmss: str, minutes: int) -> str:
    h, m, s = [int(x) for x in hhmmss.split(":")]
    base = h * 60 + m + minutes
    base %= 24 * 60
    return f"{base // 60:02d}:{base % 60:02d}:{s:02d}"


def dedupe_keep_order(values: list[str]) -> list[str]:
    out: list[str] = []
    seen: set[str] = set()
    for v in values:
        k = fold_text(v)
        if not k or k in seen:
            continue
        seen.add(k)
        out.append(v.strip())
    return out


def province_key(raw: str) -> str | None:
    t = fold_text(raw)
    if "sapa" in t or "lao cai" in t:
        return "sapa_related"
    if "ha noi" in t or "hanoi" in t or "noi bai" in t:
        return "ha_noi"
    if "ha giang" in t:
        return "ha_giang"
    if "hoi an" in t:
        return "hoi_an"
    if "da nang" in t:
        return "da_nang"
    if re.search(r"\bhue\b", t):
        return "hue"
    if "phong nha" in t:
        return "phong_nha"
    if "ninh binh" in t or "tam coc" in t:
        return "ninh_binh"
    if "cat ba" in t:
        return "cat_ba"
    if "quang ninh" in t or "tuan chau" in t:
        return "tuan_chau"
    return None


def class_to_bus_type(raw: str) -> str | None:
    t = fold_text(raw)
    if "vip 22 double cabin" in t:
        return "vip22double"
    if "vip 22 single cabin" in t:
        return "vip22single"
    if "vip 32 sleeper" in t:
        return "vip32"
    if "limousine" in t:
        return "limousine"
    if "seater" in t:
        return "seater"
    if "double cabin" in t:
        return "cabin_double"
    if "single cabin" in t:
        return "cabin_single"
    if "sleeper" in t:
        return "sleeper"
    return None


def to_vietnamese(text: str, existing_map: dict[str, str]) -> str:
    raw = (text or "").strip()
    if not raw:
        return raw
    key = fold_text(raw)

    exact_map = {
        "19 hang thiec": "19 Hàng Thiếc",
        "208 tran quang khai": "208 Trần Quang Khải",
        "210 tran quang khai": "210 Trần Quang Khải",
        "100 tran phu ha giang": "100 Trần Phú Hà Giang",
        "ben xe ha giang": "Bến xe Hà Giang",
        "hanoi airport": "Sân bay Nội Bài",
        "3 thang 2 street": "Đường 3/2",
        "28 3 thang 2 street": "28 Đường 3/2",
        "105 ton duc thang": "105 Tôn Đức Thắng",
        "7 doi cung hue": "07 Đội Cung - Huế",
        "217 mot thang tu": "217 Một Tháng Tư",
        "458 dien bien phu": "458 Điện Biên Phủ",
        "mr khanh travel agency ninh binh": "Mr Khanh Travel Agency Ninh Bình",
        "travel agency tam coc": "Travel Agency Tam Cốc",
        "tam coc boat station": "Tam Cốc Boat Station",
        "trang an boat station": "Tràng An Boat Station",
        "tuan chau harbor": "Tuần Châu Harbor",
        "dao cat ba": "Đảo Cát Bà",
        "14 hoang quoc viet – cau giay": "14 Hoàng Quốc Việt – Cầu Giấy",
        "cv hoa binh - bac tu liem": "CV Hòa Bình - Bắc Từ Liêm",
        "8 vo van kiet – soc son": "8 Võ Văn Kiệt – Sóc Sơn",
        "noi bai airport (domestic terminal - arrival e2)": "Sân bay Nội Bài (Nội địa - Arrival E2)",
        "noi bai airport (international terminal - pillar no. 19)": "Sân bay Nội Bài (Quốc tế - Cột số 19)",
    }
    if key in exact_map:
        return exact_map[key]

    out = raw
    partial_map = [
        (r"\bha giang\b", "Hà Giang"),
        (r"\bha noi\b|\bhanoi\b", "Hà Nội"),
        (r"\bhoi an\b", "Hội An"),
        (r"\bda nang\b", "Đà Nẵng"),
        (r"\bhue\b", "Huế"),
        (r"\bninh binh\b", "Ninh Bình"),
        (r"\btam coc\b", "Tam Cốc"),
        (r"\bdien bien phu\b", "Điện Biên Phủ"),
        (r"\btran quang khai\b", "Trần Quang Khải"),
        (r"\bton duc thang\b", "Tôn Đức Thắng"),
        (r"\bnoi bai\b", "Nội Bài"),
        (r"\bhoang quoc viet\b", "Hoàng Quốc Việt"),
        (r"\bhoa binh\b", "Hòa Bình"),
        (r"\bvo van kiet\b", "Võ Văn Kiệt"),
        (r"\btrang an\b", "Tràng An"),
        (r"\bcat ba\b", "Cát Bà"),
        (r"\bquang ninh\b", "Quảng Ninh"),
        (r"\btuan chau\b", "Tuần Châu"),
    ]
    for pattern, repl in partial_map:
        out = re.sub(pattern, repl, out, flags=re.IGNORECASE)
    if out != raw:
        return out
    if key in existing_map:
        return existing_map[key]
    return out


def compute_duration_label(minutes: list[int]) -> str | None:
    if not minutes:
        return None
    lo, hi = min(minutes), max(minutes)
    def fmt(x: int) -> str:
        h, m = divmod(x, 60)
        return f"{h}h" if m == 0 else f"{h}h {m}m"
    return fmt(lo) if lo == hi else f"{fmt(lo)} - {fmt(hi)}"


def build_route_content(route_name: str, agg: RouteAggregate, min_price: int, max_price: int) -> str:
    sample = sorted({f"{k[1]} - {k[2]}" for k in agg.trips.keys()})
    times = ", ".join(sample[:8]) if sample else "Liên hệ"
    pickup = "; ".join(agg.pickups[:3]) if agg.pickups else "Liên hệ"
    dropoff = "; ".join(agg.dropoffs[:3]) if agg.dropoffs else "Liên hệ"
    price_text = f"{min_price:,}".replace(",", ".") if min_price == max_price else f"{min_price:,} - {max_price:,}".replace(",", ".")
    return (
        f"<p><strong>{route_name}</strong></p>"
        f"<p>Khung giờ: {times}</p>"
        f"<p>Điểm đón: {pickup}</p>"
        f"<p>Điểm trả: {dropoff}</p>"
        f"<p>Giá vé tham khảo: {price_text} VND</p>"
    )


def load_env(path: Path) -> dict[str, str]:
    out: dict[str, str] = {}
    for line in path.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        k, v = line.split("=", 1)
        out[k.strip()] = v.strip().strip('"').strip("'")
    return out


def main() -> None:
    repo = Path(__file__).resolve().parents[1]
    xlsx = Path(r"C:\Users\minhlong\Downloads\King Express.xlsx")
    env = load_env(repo / ".env")

    df = pd.read_excel(xlsx, sheet_name="Routes by King Express Bus")
    rows = df.iloc[1:].copy()

    routes: dict[tuple[str, str], RouteAggregate] = {}
    for _, row in rows.iterrows():
        start_key = province_key(str(row.iloc[0]))
        end_key = province_key(str(row.iloc[1]))
        if not start_key or not end_key:
            continue
        if "sapa_related" in (start_key, end_key):
            continue

        bus_type = class_to_bus_type(str(row.iloc[5]))
        if not bus_type:
            continue
        departures = parse_departures(row.iloc[6])
        duration_min = parse_duration_minutes(str(row.iloc[7]))
        if not departures or duration_min is None:
            continue

        # Source sheet has 2 price blocks; effective "sel_price" matches column index 11.
        # User requires importing by "web_price", which is sel_price + 100000 in this dataset.
        sel_price = parse_price(row.iloc[11])
        if sel_price is None:
            sel_price = parse_price(row.iloc[10])
        if sel_price is None:
            continue
        import_price = sel_price + 100_000

        key = (start_key, end_key)
        agg = routes.setdefault(key, RouteAggregate(start_key=start_key, end_key=end_key))
        agg.pickups.append(str(row.iloc[2]).strip())
        agg.dropoffs.append(str(row.iloc[3]).strip())
        agg.durations.append(duration_min)
        agg.prices.append(import_price)
        for dep in departures:
            end_time = add_minutes(dep, duration_min)
            trip_key = (bus_type, dep, end_time)
            old = agg.trips.get(trip_key)
            if old is None or import_price < old:
                agg.trips[trip_key] = import_price

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

    created_routes = updated_routes = created_stops = created_trips = updated_trips = deleted_trips = 0
    created_menus = updated_menus = 0

    try:
        with conn.cursor() as cur:
            cur.execute("SELECT id, name FROM provinces")
            provinces = cur.fetchall()
            province_id_by_key: dict[str, int] = {}
            province_name_by_key: dict[str, str] = {}
            for p in provinces:
                k = province_key(p["name"])
                if k and k != "sapa_related":
                    province_id_by_key[k] = p["id"]
                    province_name_by_key[k] = p["name"]

            cur.execute("SELECT id, province_id FROM districts ORDER BY id")
            district_by_province: dict[int, int] = {}
            for d in cur.fetchall():
                district_by_province.setdefault(d["province_id"], d["id"])

            cur.execute("SELECT id, name, model_name FROM buses ORDER BY id")
            buses = cur.fetchall()
            def bus_id(match: str) -> int:
                for b in buses:
                    if match in fold_text(b["name"]) or match in fold_text(b["model_name"] or ""):
                        return b["id"]
                raise RuntimeError(f"Missing bus for {match}")
            bus_id_by_type = {
                "sleeper": bus_id("sleeper"),
                "cabin_single": bus_id("cabin single"),
                "cabin_double": bus_id("cabin double"),
                "seater": bus_id("seater"),
                "limousine": bus_id("limousine"),
                "vip32": next(b["id"] for b in buses if "vip 32" in fold_text(b["name"])),
                "vip22single": next(b["id"] for b in buses if "vip 22 cabin single" in fold_text(b["name"])),
                "vip22double": next(b["id"] for b in buses if "vip 22 cabin double" in fold_text(b["name"])),
            }

            cur.execute("SELECT s.id, s.name, s.address, d.province_id FROM stops s JOIN districts d ON d.id=s.district_id")
            existing_stops = cur.fetchall()
            accent_map: dict[str, str] = {}
            stop_by_key: dict[tuple[int, str], int] = {}
            for s in existing_stops:
                accent_map[fold_text(s["address"])] = s["address"]
                accent_map[fold_text(s["name"])] = s["name"]
                stop_by_key[(s["province_id"], fold_text(s["address"]))] = s["id"]

            # Global normalization for Vietnamese diacritics on existing stops.
            for s in existing_stops:
                norm_name = to_vietnamese(s["name"], accent_map)
                norm_address = to_vietnamese(s["address"], accent_map)
                if norm_name != s["name"] or norm_address != s["address"]:
                    cur.execute("UPDATE stops SET name=%s, address=%s, updated_at=NOW() WHERE id=%s", (norm_name, norm_address, s["id"]))
                    accent_map[fold_text(norm_address)] = norm_address
                    accent_map[fold_text(norm_name)] = norm_name
                    stop_by_key[(s["province_id"], fold_text(norm_address))] = s["id"]

            image_file_by_key = {
                "ha_noi": "ha-noi.jpg",
                "ha_giang": "ha-giang.jpg",
                "hoi_an": "hoi-an.jpg",
                "da_nang": "da-nang.jpg",
                "hue": "hue.jpg",
                "phong_nha": "phong-nha.jpg",
                "ninh_binh": "ninh-binh.jpg",
                "cat_ba": "cat-ba.jpg",
                "tuan_chau": "cat-ba.jpg",
            }

            imported_menu_items: list[tuple[int, str, str]] = []
            for idx, ((start_key, end_key), agg) in enumerate(sorted(routes.items())):
                if start_key not in province_id_by_key or end_key not in province_id_by_key:
                    continue

                start_id = province_id_by_key[start_key]
                end_id = province_id_by_key[end_key]
                start_name = province_name_by_key[start_key]
                end_name = province_name_by_key[end_key]
                route_name = f"{start_name} - {end_name}"
                route_slug = slugify(route_name)
                route_title = f"Vé xe {route_name}"
                route_description = f"Đặt vé xe chất lượng cao tuyến {route_name}"
                route_duration = compute_duration_label(agg.durations)
                avg_minutes = int(round(sum(agg.durations) / max(1, len(agg.durations))))
                route_distance = int(round((avg_minutes / 60) * 45))
                route_price_default = min(agg.prices)
                route_thumbnail = f"/client/images/city_imgs/{image_file_by_key.get(end_key, 'ha-noi.jpg')}"
                route_images = json.dumps([route_thumbnail], ensure_ascii=False)
                route_content = build_route_content(route_name, agg, min(agg.prices), max(agg.prices))

                cur.execute("SELECT id FROM routes WHERE slug=%s", (route_slug,))
                ex = cur.fetchone()
                if ex:
                    route_id = ex["id"]
                    updated_routes += 1
                    cur.execute(
                        "UPDATE routes SET province_start_id=%s, province_end_id=%s, name=%s, title=%s, description=%s, duration=%s, distance_km=%s, price_default=%s, thumbnail_url=%s, image_list_url=%s, content=%s, available_hotel_pickup=0, priority=%s, updated_at=NOW() WHERE id=%s",
                        (start_id, end_id, route_name, route_title, route_description, route_duration, route_distance, route_price_default, route_thumbnail, route_images, route_content, 60 - idx, route_id),
                    )
                else:
                    created_routes += 1
                    cur.execute(
                        "INSERT INTO routes (province_start_id, province_end_id, name, slug, title, description, duration, distance_km, price_default, thumbnail_url, image_list_url, content, available_hotel_pickup, priority, created_at, updated_at) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,0,%s,NOW(),NOW())",
                        (start_id, end_id, route_name, route_slug, route_title, route_description, route_duration, route_distance, route_price_default, route_thumbnail, route_images, route_content, 60 - idx),
                    )
                    route_id = cur.lastrowid
                imported_menu_items.append((route_id, route_name, route_slug))

                pickups = dedupe_keep_order([to_vietnamese(x, accent_map) for x in agg.pickups])
                dropoffs = dedupe_keep_order([to_vietnamese(x, accent_map) for x in agg.dropoffs])
                stop_rows: list[tuple[int, int, str, int]] = []
                for addr, stop_type, province_id in ([(p, "pickup", start_id) for p in pickups] + [(d, "dropoff", end_id) for d in dropoffs]):
                    stop_key = (province_id, fold_text(addr))
                    stop_id = stop_by_key.get(stop_key)
                    if stop_id is None:
                        created_stops += 1
                        cur.execute(
                            "INSERT INTO stops (district_id, name, address, priority, created_at, updated_at) VALUES (%s,%s,%s,0,NOW(),NOW())",
                            (district_by_province[province_id], addr, addr),
                        )
                        stop_id = cur.lastrowid
                        stop_by_key[stop_key] = stop_id
                    else:
                        cur.execute("UPDATE stops SET name=%s, address=%s, updated_at=NOW() WHERE id=%s", (addr, addr, stop_id))
                    stop_rows.append((route_id, stop_id, stop_type, len(stop_rows)))

                cur.execute("DELETE FROM route_stops WHERE route_id=%s", (route_id,))
                if stop_rows:
                    cur.executemany(
                        "INSERT INTO route_stops (route_id, stop_id, stop_type, priority, created_at, updated_at) VALUES (%s,%s,%s,%s,NOW(),NOW())",
                        stop_rows,
                    )

                cur.execute("SELECT id, bus_id, start_time, end_time FROM trips WHERE route_id=%s", (route_id,))
                existing_trips = {}
                for t in cur.fetchall():
                    st = t["start_time"]
                    et = t["end_time"]
                    if isinstance(st, timedelta):
                        st = f"{int(st.total_seconds() // 3600):02d}:{int((st.total_seconds() % 3600) // 60):02d}:00"
                    else:
                        st = re.sub(r"^(\d):", r"0\1:", str(st))
                    if isinstance(et, timedelta):
                        et = f"{int(et.total_seconds() // 3600):02d}:{int((et.total_seconds() % 3600) // 60):02d}:00"
                    else:
                        et = re.sub(r"^(\d):", r"0\1:", str(et))
                    existing_trips[(t["bus_id"], st, et)] = t["id"]

                desired: set[tuple[int, str, str]] = set()
                for (bus_type, start_time, end_time), price in agg.trips.items():
                    bus_id_val = bus_id_by_type[bus_type]
                    key = (bus_id_val, start_time, end_time)
                    desired.add(key)
                    if key in existing_trips:
                        updated_trips += 1
                        cur.execute("UPDATE trips SET price=%s, is_active=1, updated_at=NOW() WHERE id=%s", (price, existing_trips[key]))
                    else:
                        created_trips += 1
                        cur.execute(
                            "INSERT INTO trips (bus_id, route_id, start_time, end_time, price, is_active, priority, created_at, updated_at) VALUES (%s,%s,%s,%s,%s,1,0,NOW(),NOW())",
                            (bus_id_val, route_id, start_time, end_time, price),
                        )
                for key, trip_id in existing_trips.items():
                    if key not in desired:
                        deleted_trips += 1
                        cur.execute("DELETE FROM trips WHERE id=%s", (trip_id,))

            cur.execute("SELECT id FROM menus WHERE parent_id IS NULL AND name=%s LIMIT 1", ("Tuyến đường",))
            parent = cur.fetchone()
            if parent:
                parent_id = parent["id"]
            else:
                cur.execute(
                    "INSERT INTO menus (name, url, parent_id, priority, type, related_id, created_at, updated_at) VALUES ('Tuyến đường','#',NULL,1,'custom_link',NULL,NOW(),NOW())"
                )
                parent_id = cur.lastrowid

            cur.execute("SELECT COALESCE(MAX(priority), -1) as p FROM menus WHERE parent_id=%s", (parent_id,))
            base_priority = int(cur.fetchone()["p"]) + 1
            cur.execute("SELECT id, related_id FROM menus WHERE parent_id=%s AND type='route'", (parent_id,))
            menu_by_route = {m["related_id"]: m["id"] for m in cur.fetchall() if m["related_id"] is not None}
            for offset, (route_id, name, slug) in enumerate(imported_menu_items):
                url = f"/tuyen-duong/{slug}"
                if route_id in menu_by_route:
                    updated_menus += 1
                    cur.execute("UPDATE menus SET name=%s, url=%s, updated_at=NOW() WHERE id=%s", (name, url, menu_by_route[route_id]))
                else:
                    created_menus += 1
                    cur.execute(
                        "INSERT INTO menus (name, url, parent_id, priority, type, related_id, created_at, updated_at) VALUES (%s,%s,%s,%s,'route',%s,NOW(),NOW())",
                        (name, url, parent_id, base_priority + offset, route_id),
                    )

        conn.commit()
    finally:
        conn.close()

    print("IMPORT_DONE")
    print(f"routes_imported={len(routes)} routes_created={created_routes} routes_updated={updated_routes}")
    print(f"trips_created={created_trips} trips_updated={updated_trips} trips_deleted={deleted_trips}")
    print(f"stops_created={created_stops}")
    print(f"menus_created={created_menus} menus_updated={updated_menus}")


if __name__ == "__main__":
    main()
