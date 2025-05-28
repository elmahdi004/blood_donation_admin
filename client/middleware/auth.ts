import { useAuth } from "../composables/useAuth";

export default defineNuxtRouteMiddleware(async (to, from) => {
  const publicPages = ["/login", "/register", "/forbidden"];
  const { user, checkAuth, userType } = useAuth();

  // Wait for user info if not already loaded
  if (!user.value) {
    await checkAuth();
  }

  // If not logged in and not on a public page, redirect to login
  if (!user.value && !publicPages.includes(to.path)) {
    return navigateTo("/login");
  }

  // If logged in but not admin or blood_bank, block access
  if (user.value && !userType.value && !publicPages.includes(to.path)) {
    return navigateTo("/forbidden");
  }

  // If blood bank, only allow access to /customers and related routes
  if (userType.value === 'blood_bank' && !to.path.startsWith('/customers') && !publicPages.includes(to.path)) {
    return navigateTo("/forbidden");
  }

  // If logged in and trying to access login/register, redirect to appropriate page
  if (user.value && publicPages.includes(to.path)) {
    if (userType.value === 'blood_bank') {
      return navigateTo("/customers");
    }
    return navigateTo("/");
  }
});
