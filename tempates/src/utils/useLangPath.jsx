import useLang from "@/context/useLang";

export default function useLangPath() {
  const { lang } = useLang();

  const path = (slug, ext = "") => {
    if (!slug) return `/${lang}`;
    return `/${lang}/${slug}${ext}`;
  };

  return path;
}
