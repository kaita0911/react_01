import { useParams } from "react-router-dom";
import { useLanguage } from "@/context/useLanguage";

export default function useLangPath() {
  const { lang: urlLang } = useParams();
  const { defaultLang, singleLang } = useLanguage();

  const lang = urlLang || defaultLang;

  const path = (slug = "", ext = "") => {
    // 🔥 chỉ 1 ngôn ngữ → bỏ lang khỏi URL
    if (singleLang) {
      if (!slug) return "/";
      return `/${slug}${ext}`;
    }

    // 🔥 nhiều ngôn ngữ
    if (!lang) return "/";
    if (!slug) return `/${lang}`;

    return `/${lang}/${slug}${ext}`;
  };

  return path;
}
