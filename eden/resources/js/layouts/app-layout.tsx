import { Link } from '@mui/material';
import { Outlet } from 'react-router-dom';

export default function AppLayout() {
    return (
        <div>
            <nav className="flex gap-2 p-2">
                <Link href='/' underline='hover'>Home</Link>
                <Link href='/dashboard' underline='hover'>Dashboard</Link>
            </nav>
            <Outlet />
        </div>
    );
}
