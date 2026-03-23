import LanguageContext from "./LanguageContext";

export const LanguageProvider = ({
  children,
  languages,
  singleLang,
  defaultLang,
}) => {
  return (
    <LanguageContext.Provider value={{ languages, singleLang, defaultLang }}>
      {children}
    </LanguageContext.Provider>
  );
};
