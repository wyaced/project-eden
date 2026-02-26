export default function AppSidebarLayout({ children }: { children: React.ReactNode }) {
    return (
        <div className="flex">
            <aside className="w-64">Sidebar content</aside>
            <main className="flex-1">
                {children}
            </main>
        </div>
    );
}