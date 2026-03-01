import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Dashboard from '@/pages/DashboardTest';
import Home from '@/pages/welcome';
import AppLayout from './layouts/app-layout';
import Listings from './pages/listings';

export default function AppRouting() {
    return (
        <BrowserRouter>
            <Routes>
                <Route element={<AppLayout />}>
                    <Route index element={<Home />} />
                    <Route path="/dashboard" element={<Dashboard />} />
                    <Route path="/listings" element={<Listings />} />
                </Route>
            </Routes>
        </BrowserRouter>
    );
}
