import { useEffect } from "react"

function Seo({
  title = "Default Title",
  description = "",
  keywords = "",
  image = ""
}) {

  useEffect(() => {
    const url = window.location.href
    const ogImage = image || "/OGP.jpg"
    document.title = title

    const setMeta = (attr, name, content) => {
      let element = document.querySelector(`meta[${attr}="${name}"]`)

      if (!element) {
        element = document.createElement("meta")
        element.setAttribute(attr, name)
        document.head.appendChild(element)
      }

      element.setAttribute("content", content || "")
    }

    // Basic SEO
    setMeta("name", "description", description)
    setMeta("name", "keywords", keywords)

    // Open Graph (Facebook)
    setMeta("property", "og:type", "website")
    setMeta("property", "og:title", title)
    setMeta("property", "og:description", description)
    setMeta("property", "og:image", ogImage)
    setMeta("property", "og:url", url)

    // Twitter
    setMeta("name", "twitter:card", "summary_large_image")
  // ===== image_src LINK =====
  let link = document.querySelector('link[rel="image_src"]')
  if (!link) {
    link = document.createElement("link")
    link.setAttribute("rel", "image_src")
    link.setAttribute("type", "image/jpeg")
    document.head.appendChild(link)
  }
  link.setAttribute("href", ogImage)
  }, [title, description, keywords, image])

  return null
}

export default Seo