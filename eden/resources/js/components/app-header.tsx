import { Link } from "react-router-dom";
import AppLogo from "./app-logo";

export default function AppHeader() {

    return (
        <header className="border-b border-sidebar-border/80">
            <div className="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <Link to="/" className="flex items-center mr-6">
                    <AppLogo />
                </Link>

                <nav className="flex space-x-4">
                    <Link to="/">Home</Link>
                    <Link to="/dashboard">Dashboard</Link>
                </nav>
            </div>
        </header>
    );
}