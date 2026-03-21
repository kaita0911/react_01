import { useEffect } from "react";

export default function useScrollToTop(dep) {
  useEffect(() => {
    // window.scrollTo({ top: 0, behavior: "smooth" });
    window.scrollTo(0, 0);
  }, [dep]);
}
