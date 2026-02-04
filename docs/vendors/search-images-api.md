# Image Search API

A FastAPI-based RESTful service for searching and optimizing images from the web.

## Features

- **Image Search**: Search images using DuckDuckGo with keyword queries
- **Image Optimization**: Automatically optimize images for web display (resize, compress, convert to WebP)
- **Base64 Encoding**: Return optimized images as base64 data URLs for easy embedding
- **Rate Limit Handling**: Built-in exponential backoff for API rate limits
- **RESTful Design**: Clean API structure following REST best practices

## Project Structure

```
├── app/
│   ├── __init__.py
│   ├── main.py              # FastAPI application entry point
│   ├── api/
│   │   ├── __init__.py
│   │   └── routes/
│   │       ├── __init__.py
│   │       └── images.py    # Image search endpoints
│   ├── core/
│   │   ├── __init__.py
│   │   └── config.py        # Application configuration
│   ├── schemas/
│   │   ├── __init__.py
│   │   └── images.py        # Pydantic request/response models
│   ├── services/
│   │   ├── __init__.py
│   │   ├── image_search.py  # DuckDuckGo search service
│   │   └── image_optimizer.py # Image optimization service
│   └── utils/
│       ├── __init__.py
│       └── helpers.py       # Utility functions
├── requirements.txt
├── .env.example
└── README.md
```

## Installation

1. **Clone the repository**
   ```bash
   cd google-search-images-using-vn-provinces-wards
   ```

2. **Create virtual environment**
   ```bash
   python -m venv venv
   source venv/bin/activate  # Linux/Mac
   venv\Scripts\activate     # Windows
   ```

3. **Install dependencies**
   ```bash
   pip install -r requirements.txt
   ```

4. **Configure environment (optional)**
   ```bash
   cp .env.example .env
   # Edit .env with your settings
   ```

## Running the Server

```bash
# Development mode with auto-reload
uvicorn app.main:app --reload --host 0.0.0.0 --port 8888

# Or using Python
python -m app.main
```

The API will be available at `http://localhost:8888`

## API Documentation

- **Swagger UI**: http://localhost:8888/docs
- **ReDoc**: http://localhost:8888/redoc

## API Endpoints

### POST /search-images

Search for images and return optimized results.

**Request Body:**
```json
{
  "query": "beautiful sunset beach",
  "count": 10,
  "optimize": true
}
```

**Response:**
```json
{
  "success": true,
  "query": "beautiful sunset beach",
  "count": 10,
  "images": [
    {
      "url": "https://example.com/image.jpg",
      "thumbnail_url": "https://example.com/thumb.jpg",
      "title": "Beautiful Sunset",
      "source_url": "https://example.com/page",
      "width": 1200,
      "height": 800,
      "content_type": "image/webp",
      "size_bytes": 45000,
      "base64_data": "data:image/webp;base64,..."
    }
  ],
  "message": null
}
```

### POST /api/v1/images/search

Same as `/search-images` (versioned endpoint).

### POST /api/v1/images/search-urls

Search for image URLs only (faster, no optimization).

### GET /health

Health check endpoint.

## Configuration

Configure via environment variables or `.env` file:

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_NAME` | Image Search API | Application name |
| `DEFAULT_IMAGE_COUNT` | 10 | Default number of images |
| `MAX_IMAGE_COUNT` | 20 | Maximum images per request |
| `MAX_IMAGE_WIDTH` | 1200 | Maximum image width |
| `MAX_IMAGE_HEIGHT` | 800 | Maximum image height |
| `JPEG_QUALITY` | 85 | JPEG compression quality |
| `WEBP_QUALITY` | 80 | WebP compression quality |
| `HTTP_TIMEOUT_SECONDS` | 15 | HTTP request timeout |

## Usage Examples

### cURL

```bash
# Search with optimization
curl -X POST "http://localhost:8888/search-images" \
  -H "Content-Type: application/json" \
  -d '{"query": "Vietnam landscape", "count": 5}'

# Search URLs only (faster)
curl -X POST "http://localhost:8888/api/v1/images/search-urls" \
  -H "Content-Type: application/json" \
  -d '{"query": "Hanoi city", "count": 10}'
```

### Python

```python
import requests

response = requests.post(
    "http://localhost:8888/search-images",
    json={"query": "Da Nang beach", "count": 10, "optimize": True}
)

data = response.json()
for image in data["images"]:
    print(f"URL: {image['url']}")
    if image.get("base64_data"):
        # Use base64 data directly in HTML
        # <img src="{image['base64_data']}" />
        pass
```

### JavaScript/Fetch

```javascript
const response = await fetch('http://localhost:8888/search-images', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    query: 'Ho Chi Minh City',
    count: 10,
    optimize: true
  })
});

const data = await response.json();
data.images.forEach(image => {
  // Use image.base64_data as img src
  const img = document.createElement('img');
  img.src = image.base64_data || image.url;
  document.body.appendChild(img);
});
```

## License

MIT
