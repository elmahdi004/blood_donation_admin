// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: [
    "@nuxt/eslint",
    "@nuxt/ui-pro",
    "@vueuse/nuxt",
    // "@nuxtjs/axios",
    // "@sidebase/nuxt-auth",
  ],

  devtools: {
    enabled: true,
  },
  router: {
    // middleware: ["auth.global"], // applies to every route
  },
  css: ["~/assets/css/main.css"],

  // routeRules: {
  //   "/api/**": {
  //     cors: true,
  //   },
  //   "/": { middleware: ["auth"] },
  //   "/settings/**": { middleware: ["auth"] },
  //   "/demandes/**": { middleware: ["auth"] },
  //   "/customers/**": { middleware: ["auth"] },
  //   "/login": { middleware: ["auth"] },
  //   "/register": { middleware: ["auth"] },
  // },

  future: {
    compatibilityVersion: 4,
  },

  compatibilityDate: "2024-07-11",

  eslint: {
    config: {
      stylistic: {
        commaDangle: "never",
        braceStyle: "1tbs",
      },
    },
  },

  runtimeConfig: {
    public: {
      apiBase: process.env.API_BASE || "http://localhost:8000/api",
    },
  },

  typescript: {
    strict: true,
    typeCheck: true,
  },

  build: {
    transpile: ["axios"],
  },

  vite: {
    optimizeDeps: {
      include: ["axios"],
    },
  },
});
