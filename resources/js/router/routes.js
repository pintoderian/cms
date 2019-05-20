const Dashboard = () => import("~/pages/dashboard").then(m => m.default || m);
const Login = () => import("~/pages/auth/login").then(m => m.default || m);
const Register = () => import("~/pages/auth/register").then(m => m.default || m);
const PasswordEmail = () => import("~/pages/auth/password/email").then(m => m.default || m);
const PasswordReset = () => import("~/pages/auth/password/reset").then(m => m.default || m);
const NotFound = () => import("~/pages/errors/404").then(m => m.default || m);

//const Home = () => import("~/pages/home").then(m => m.default || m);
const Settings = () => import("~/pages/settings/index").then(m => m.default || m);
const SettingsProfile = () => import("~/pages/settings/profile").then(m => m.default || m);
const SettingsPassword = () => import("~/pages/settings/password").then(m => m.default || m);

const SettingsCatalog = () => import("~/pages/core/catalog").then(m => m.default || m);

const Roles = () => import("~/pages/core/roles").then(m => m.default || m);
const Users = () => import("~/pages/core/users").then(m => m.default || m);

/**
 * Logs
 */
const LogUserAccess = () => import("~/pages/core/logs/access").then(m => m.default || m);
const LogUserActivity = () => import("~/pages/core/logs/activity_user").then(m => m.default || m);
const LogSystem = () => import("~/pages/core/logs/system").then(m => m.default || m);
const LogSystemView = () => import('~/pages/core/logs/system_view').then(m => m.default || m)

const principal = [
  { path: "/", name: "dashboard", component: Dashboard },

  { path: "/login", name: "login", component: Login },
  { path: "/register", name: "register", component: Register },
  {
    path: "/password/reset",
    name: "password.request",
    component: PasswordEmail
  },
  {
    path: "/password/reset/:token",
    name: "password.reset",
    component: PasswordReset
  },

  //{ path: "/home", name: "home", component: Home },
  { path: "/core/roles", name: "core.roles", component: Roles },
  { path: "/core/users", name: "core.users", component: Users },
  { path: "/logs/user-access", name: "logs.user.access", component: LogUserAccess },
  { path: "/logs/user-activity", name: "logs.user.activity", component: LogUserActivity },
  { path: "/logs/system", name: "logs.system", component: LogSystem },
  { path: '/logs/system-view-logs/:date/:type', name: 'logs.system.view', component: LogSystemView },
  {
    path: "/settings",
    component: Settings,
    children: [
      { path: "", redirect: { name: "settings.profile" } },
      { path: "profile", name: "settings.profile", component: SettingsProfile },
      {
        path: "password",
        name: "settings.password",
        component: SettingsPassword
      },
    ]
  },
  { path: "/settings/catalogs", name: "settings.catalogs", component: SettingsCatalog },
  { path: "*", component: NotFound }
];

var merge_routes = function (routes) {
  routes = Array.prototype.slice.call(arguments);
  return routes.reduce(function (prev, next) {
    return prev.concat(next);
  });
};
/*
* Se importan los demás módulos
* */
import anime_module from '~/modules/anime/routes';

/**
 * Se unen todas las rutas
 * */
const final_routes = merge_routes(principal, anime_module);
export default final_routes;