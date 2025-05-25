// export default defineNuxtRouteMiddleware((to, from) => {

//     const storedToken = localStorage.getItem("token");

//     // If no token and trying to access a protected route
//     if (!storedToken && to.path !== "/login" && to.path !== "/register") {
//       console.log("No token and trying to access a protected route");
//       return navigateTo("/login");
//     }

//     // If has token and trying to access auth pages
//     if (storedToken && (to.path === "/login" || to.path === "/register")) {
//       console.log("Has token and trying to access auth pages");
//       return navigateTo("/");
//     }
//   }
// );
