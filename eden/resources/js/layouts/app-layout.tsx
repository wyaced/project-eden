import { Outlet } from "react-router-dom";
import AppHeaderLayout from "./app/app-header-layout";
import AppSidebarLayout from "./app/app-sidebar-layout";

export default function AppLayout() {
    return (
        <AppHeaderLayout>
            <AppSidebarLayout>
                <div>
                    <h1>Layout Working</h1>
                    <Outlet />
                </div>
            </AppSidebarLayout>
        </AppHeaderLayout>
    );
}