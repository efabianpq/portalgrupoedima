/**
 * Tokens de marca — Grupo Edima
 * Paleta: petróleo (primario) + acero (secundario) + ámbar (acento) + neutros cálidos.
 * Tipografía: Lora (títulos) + Karla (texto).
 *
 * Todo el sitio debe consumir estos tokens (colors.brand.*, fontFamily.heading/body,
 * borderRadius, boxShadow) en lugar de valores hex o clases arbitrarias sueltas.
 */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          // Escalas derivadas del logo real de Grupo Edima (icono de triángulos).
          primary: {
            50: "#F2F6F7",
            100: "#DDEAEE",
            200: "#AFD8E4",
            300: "#6CC5E0",
            400: "#19B6E6",
            500: "#0C96C0",
            600: "#07799C",
            700: "#046380", // = color exacto del triángulo teal del logo (botones, links, header)
            800: "#044458",
            900: "#032A35",
          },
          secondary: {
            50: "#F1F6F9",
            100: "#D7EAF4",
            200: "#A8D7F0",
            300: "#71C7F4",
            400: "#38B7FA",
            500: "#20B2FF", // = color exacto del triángulo azul del logo
            600: "#069EEF",
            700: "#0A7CB8",
            800: "#0B5A84",
            900: "#093953",
          },
          accent: {
            50: "#F9F6F0",
            100: "#F5E9D6",
            200: "#F6D9AC",
            300: "#F8C981",
            400: "#FFBD59", // = color exacto del triángulo ámbar del logo
            500: "#FAA92E",
            600: "#E9910C",
            700: "#AA6C0E",
          },
          neutral: {
            50: "#FAF8F5",
            100: "#F3EFE9",
            200: "#E7E0D6",
            300: "#D2C8B9",
            400: "#A79C89",
            500: "#7C7360",
            600: "#5B5347",
            700: "#423B31",
            800: "#2B261F",
            900: "#1B1712",
          },
        },
      },
      fontFamily: {
        heading: ["Lora", "Georgia", "Cambria", "Times New Roman", "serif"],
        body: [
          "Karla",
          "-apple-system",
          "BlinkMacSystemFont",
          "Segoe UI",
          "Helvetica Neue",
          "Arial",
          "sans-serif",
        ],
      },
      borderRadius: {
        sm: "6px",
        DEFAULT: "8px", // botones
        md: "8px",
        lg: "12px", // tarjetas
        xl: "16px", // paneles grandes / hero
        pill: "999px", // badges
      },
      boxShadow: {
        sm: "0 1px 2px 0 rgba(27, 23, 18, 0.06), 0 1px 1px 0 rgba(27, 23, 18, 0.04)",
        DEFAULT:
          "0 2px 8px -1px rgba(27, 23, 18, 0.08), 0 1px 3px -1px rgba(27, 23, 18, 0.06)",
        md: "0 4px 14px -2px rgba(27, 23, 18, 0.10), 0 2px 6px -2px rgba(27, 23, 18, 0.06)",
        lg: "0 12px 28px -6px rgba(27, 23, 18, 0.16), 0 4px 10px -4px rgba(27, 23, 18, 0.08)",
      },
    },
  },
  plugins: [],
};
