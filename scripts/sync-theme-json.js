// scripts/sync-theme-json.js
const fs = require("fs");
const path = require("path");
const { pathToFileURL } = require("url");

(async () => {
  const root    = path.resolve(__dirname, "..");
  const cjsPath = path.join(root, "tailwind.config.cjs");
  const jsPath  = path.join(root, "tailwind.config.js");

  let config;

  if (fs.existsSync(cjsPath)) {
    // klassische CJS-Config
    config = require(cjsPath);
  } else if (fs.existsSync(jsPath)) {
    // erst versuchen, als CJS zu laden …
    try {
      config = require(jsPath);
    } catch (_) {
      // … andernfalls als ESM importieren
      const mod = await import(pathToFileURL(jsPath) + "?cb=" + Date.now());
      config = mod.default ?? mod;
    }
  } else {
    console.warn("⚠️  Keine Tailwind-Config gefunden (tailwind.config.cjs/js). Erzeuge leeres theme.json.");
    config = {};
  }

  // defensives Auslesen (Tailwind v3/v4)
  const colors   = config?.theme?.extend?.colors   ?? {};
  const fontSize = config?.theme?.extend?.fontSize ?? {};

  // Farbpalette normalisieren (string oder {DEFAULT, ...})
  const palette = Object.entries(colors).map(([slug, color]) => {
    const value = typeof color === "string" ? color : (color?.DEFAULT ?? "");
    return {
      name: slug.charAt(0).toUpperCase() + slug.slice(1),
      slug,
      color: value
    };
  });

  // Font-Sizes normalisieren (["1rem",{…}] | {value:"1rem"} | "1rem")
  const fontSizes = Object.entries(fontSize).map(([slug, size]) => {
    const val =
      Array.isArray(size) ? size[0] :
      (typeof size === "object" ? (size?.value ?? "") : size);

    // "sm" -> "Sm", "display-lg" -> "Display-Lg"
    const pretty = slug.replace(/(^.|-.)/g, s => s.toUpperCase());

    return { name: pretty, slug, size: val };
  });

  const themeJson = {
    $schema: "https://schemas.wp.org/trunk/theme.json",
    version: 3,
    settings: {
      layout: { contentSize: "960px", wideSize: "1280px" },
      color: { palette },
      typography: { fontSizes }
    }
  };

  const outFile = path.join(root, "theme.json");
  fs.writeFileSync(outFile, JSON.stringify(themeJson, null, 2));
  console.log("✅ theme.json synchronisiert.", `(Palette: ${palette.length}, FontSizes: ${fontSizes.length})`);
})();
