export default defineNuxtRouteMiddleware((to, from) => {
  const token = localStorage.getItem("token");

  // If no token and trying to access a protected route
  if (!token && to.path !== "/login" && to.path !== "/register") {
    return navigateTo("/login");
  }

  // If has token and trying to access auth pages
  if (token && (to.path === "/login" || to.path === "/register")) {
    return navigateTo("/dashboard");
  }
});
