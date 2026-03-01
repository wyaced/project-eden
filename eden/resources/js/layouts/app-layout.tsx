import { Outlet } from 'react-router-dom';
import NavBar from '@/components/eden-components/navbar';

export default function AppLayout() {
    return (
        <div>
            <NavBar pages={[
                    ['Home', '/'],
                    ['Listings', '/listings'],
                ]}
            />
            <Outlet />
        </div>
    );
}
