import Header from "@/components/Header";
import Footer from "@/components/Footer";
import TabMenu from "@/components/tabMenu";

export const metadata = {
  title: "Livros Viajantes",
  description: "Compartilhe e descubra livros que viajam pelo mundo!",
};

export default function RootLayout({ children }) {
  return (
    <html lang="pt-BR">
      <body>
        <Header />

        <main>{children}</main>
        <Footer />
        <TabMenu />
      </body>
    </html>
  );
}