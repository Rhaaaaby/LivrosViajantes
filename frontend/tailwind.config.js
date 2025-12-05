/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/pages/**/*.{js,ts,jsx,tsx}",
    "./src/components/**/*.{js,ts,jsx,tsx}",
    "./src/app/**/*.{js,ts,jsx,tsx}",
  ],

  theme: {
    extend: {
      colors: {
        site: {
          plano_fundo: "#F2F5EE",
          titulo: "#1E5376",
          subtitulo: "#306B99",
        },
      },
      fontFamily: {
        titulo: ['"Playfair Display"', "serif"],
        corpo: ["Inter", "sans-serif"],
      },
    },
  },
  plugins: [],
}