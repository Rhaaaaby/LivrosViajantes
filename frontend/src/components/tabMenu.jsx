import { usePathname } from "next/navigation";
import Link from "next/link";

const tabs = [
    {label: "Home", path: "/"},
    {label: "Publicar", path:"/publicar"},
    {label: "Pesquisar", path: "/pesquisar"},
];

export default function TabMenu() {
  const pathname = usePathname(); 

  return (
    <nav>
      {tabs.map(tab=> (
        <Link key={tab.path} href={tab.path}>
            {tab.label}
        </Link>
      ))}
    </nav>
  );
}