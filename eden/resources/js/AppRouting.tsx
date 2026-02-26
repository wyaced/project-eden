import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Dashboard from '@/pages/DashboardTest';
import Home from '@/pages/welcome';
import AppLayout from './layouts/app-layout';

export default function AppRouting() {
    return (
        <BrowserRouter>
            <Routes>
                <Route element={<AppLayout />}>
                    <Route index element={<Home />} />
                    <Route path="/dashboard" element={<Dashboard />} />
                </Route>
            </Routes>
        </BrowserRouter>
    );
}
