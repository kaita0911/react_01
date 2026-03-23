// src/hooks/useLang.js
import { useParams } from "react-router-dom";
import { useLanguage } from "@/context/useLanguage";
import { i18n } from "@/utils/i18n";

export default function useLang() {
  const { lang: urlLang } = useParams();
  const { defaultLang, singleLang } = useLanguage();

  // 🔥 logic chuẩn
  const currentLang = singleLang ? defaultLang : urlLang || defaultLang;

  const t = i18n[currentLang] || i18n[defaultLang] || {};

  return { lang: currentLang, t };
}
