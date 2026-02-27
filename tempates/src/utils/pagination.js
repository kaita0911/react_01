export function getPages(current, total) {
    const pages = []
  
    if (total <= 7) {
      for (let i = 1; i <= total; i++) pages.push(i)
      return pages
    }
  
    pages.push(1)
  
    if (current > 4) pages.push("...")
  
    const start = Math.max(2, current - 1)
    const end = Math.min(total - 1, current + 1)
  
    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
  
    if (current < total - 3) pages.push("...")
  
    pages.push(total)
  
    return pages
  }
  