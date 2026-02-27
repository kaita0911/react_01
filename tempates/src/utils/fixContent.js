import { API_URL } from "@/config"

export function fixContent(html) {
  if (!html) return ""

  const parser = new DOMParser()
  const doc = parser.parseFromString(html, "text/html")

  // Fix ảnh relative → absolute
  doc.querySelectorAll("img").forEach(img => {
    const src = img.getAttribute("src")
    if (src && src.startsWith("/")) {
      img.src = API_URL + src
    }
  })

  return doc.body.innerHTML
}
