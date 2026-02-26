import { BrowserRouter, Routes, Route } from "react-router-dom";
import AppLayout from "./layouts/app-layout";
import Home from "@/pages/welcome";
import Dashboard from "@/pages/DashboardTest";

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