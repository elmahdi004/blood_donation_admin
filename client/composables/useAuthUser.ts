import { useState } from "nuxt/app";
import { watch } from "vue";

export function useAuthUser() {
  const user = useState("authUser", () => {
    if (process.client) {
      const stored = localStorage.getItem("user");
      if (stored) return JSON.parse(stored);
    }
    return null;
  });

  if (process.client) {
    watch(
      user,
      (val) => {
        if (val) {
          localStorage.setItem("user", JSON.stringify(val));
        } else {
          localStorage.removeItem("user");
        }
      },
      { deep: true }
    );
  }

  return user;
}
