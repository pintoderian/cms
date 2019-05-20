const anime_lists = () => import("~/modules/anime/pages/anime").then(m => m.default || m);

export default [
    { path: "/anime/lists", name: "anime.lists", component: anime_lists },
];