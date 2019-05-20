<script>
    const sidebarMenu = [
        {path: "/", icon: "fa fa-th", title: "Inicio", permission: "access_panel"},
        {
            path: "/core",
            icon: "fa fa-users",
            title: "Usuarios",
            permission: ["view_roles", "view_users"],
            children: [
                {path: "/core/roles", title: "Roles", permission: "view_roles"},
                {path: "/core/users", title: "Listado", permission: "view_users"}
            ]
        },
        {
            path: "/logs",
            icon: "fa fa-binoculars",
            title: "Logs",
            permission: ["logs_user_access"],
            children: [
                {
                    path: "/logs/user-access",
                    title: "Acceso de usuarios",
                    permission: "logs_user_access"
                },
                {
                    path: "/logs/user-activity",
                    title: "Actividad de usuarios",
                    permission: "logs_user_activity"
                },
                {
                    path: "/logs/system",
                    title: "Logs del Sistema",
                    permission: "logs_system"
                }
            ]
        },
        {
            path: "/settings",
            icon: "fa fa-cogs",
            title: "Ajustes",
            permission: ["settings_catalogs"],
            children: [
                {
                    path: "/settings/catalogs",
                    title: "Catálogos",
                    permission: "settings_catalogs"
                },
            ]
        }
    ];

    export default sidebarMenu;
</script>
