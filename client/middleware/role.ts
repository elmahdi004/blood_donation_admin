import { useAuthUser } from "~/composables/useAuthUser"; // adjust path if needed

export default defineNuxtRouteMiddleware((to) => {
  const user = useAuthUser();
  const allowedRoles = to.meta.allowedRoles as string[] | undefined;
  if (
    allowedRoles &&
    (!user.value || !allowedRoles.includes(user.value.type))
  ) {
    return navigateTo("/forbidden");
  }
});
