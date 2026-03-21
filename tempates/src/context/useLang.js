// src/hooks/useLang.js
import { useParams } from "react-router-dom";
import { i18n } from "@/utils/i18n";

export default function useLang() {
  const { lang } = useParams();

  const currentLang = lang || "vi";
  const t = i18n[currentLang] || i18n.vi;

  return { lang: currentLang, t };
}
