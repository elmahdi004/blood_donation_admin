<script setup lang="ts">
import { useAuthUser } from "../composables/useAuthUser";

const route = useRoute();
const toast = useToast();
const authUser = useAuthUser();

const getUserType = () => {
  if (process.client) {
    return localStorage.getItem("userType");
  }
  return null;
};
const userType = ref(getUserType());

// Optionally, keep userType in sync with localStorage changes
if (process.client) {
  window.addEventListener("storage", () => {
    userType.value = localStorage.getItem("userType");
  });
}

const open = ref(false);

const links = [
  [
    {
      label: "Home",
      icon: "i-lucide-house",
      to: "/",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      label: "Demandes",
      icon: "i-lucide-heart-plus",
      to: "/demandes",
      onSelect: () => {
        open.value = false;
      },
    },
    ...(userType.value === "blood_bank"
      ? [
          {
            label: "Gestion du Stock",
            icon: "i-lucide-droplet",
            to: "/stock",
            onSelect: () => {
              open.value = false;
            },
          },
          {
            label: "Rendez-vous",
            icon: "i-lucide-droplet",
            to: "/rendez-vous",
            onSelect: () => {
              open.value = false;
            },
          },
          {
            label: "Creneaux disponibles",
            icon: "i-lucide-droplet",
            to: "/slots",
            onSelect: () => {
              open.value = false;
            },
          },
          {
            label: "Charts",
            icon: "i-lucide-charts",
            to: "/charts",
            onSelect: () => {
              open.value = false;
            },
          },
        ]
      : []),
    ...(userType.value === "admin"
      ? [
          {
            label: "Blood Banks",
            icon: "i-lucide-heart-plus",
            to: "/blood-banks",
            onSelect: () => {
              open.value = false;
            },
          },
        ]
      : []),
    {
      label: "Users",
      icon: "i-lucide-users",
      to: "/users",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      label: "Intention Donation",
      icon: "i-lucide-users",
      to: "/intention-dons",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      label: "Inbox",
      icon: "i-lucide-inbox",
      to: "/inbox",
      badge: "4",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      label: "Customers",
      icon: "i-lucide-users",
      to: "/customers",
      onSelect: () => {
        open.value = false;
      },
    },
    {
      label: "Settings",
      to: "/settings",
      icon: "i-lucide-settings",
      defaultOpen: true,
      children: [
        {
          label: "General",
          to: "/settings",
          exact: true,
          onSelect: () => {
            open.value = false;
          },
        },
        {
          label: "Members",
          to: "/settings/members",
          onSelect: () => {
            open.value = false;
          },
        },
        {
          label: "Notifications",
          to: "/settings/notifications",
          onSelect: () => {
            open.value = false;
          },
        },
        {
          label: "Security",
          to: "/settings/security",
          onSelect: () => {
            open.value = false;
          },
        },
      ],
    },
  ],
  [
    {
      label: "Feedback",
      icon: "i-lucide-message-circle",
      to: "https://github.com/nuxt-ui-pro/dashboard",
      target: "_blank",
    },
    {
      label: "Help & Support",
      icon: "i-lucide-info",
      to: "https://github.com/nuxt/ui-pro",
      target: "_blank",
    },
  ],
];

const groups = computed(() => [
  {
    id: "links",
    label: "Go to",
    items: links.flat(),
  },
  {
    id: "code",
    label: "Code",
    items: [
      {
        id: "source",
        label: "View page source",
        icon: "i-simple-icons-github",
        to: `https://github.com/nuxt-ui-pro/dashboard/blob/main/app/pages${
          route.path === "/" ? "/index" : route.path
        }.vue`,
        target: "_blank",
      },
    ],
  },
]);

onMounted(async () => {
  const cookie = useCookie("cookie-consent");
  if (cookie.value === "accepted") {
    return;
  }

  toast.add({
    title:
      "We use first-party cookies to enhance your experience on our website.",
    duration: 0,
    close: false,
    actions: [
      {
        label: "Accept",
        color: "neutral",
        variant: "outline",
        onClick: () => {
          cookie.value = "accepted";
        },
      },
      {
        label: "Opt out",
        color: "neutral",
        variant: "ghost",
      },
    ],
  });
});
</script>

<template>
  <UDashboardGroup unit="rem">
    <UDashboardSidebar
      id="default"
      v-model:open="open"
      collapsible
      resizable
      class="bg-(--ui-bg-elevated)/25"
      :ui="{ footer: 'lg:border-t lg:border-(--ui-border)' }"
    >
      <template #header="{ collapsed }">
        <TeamsMenu :collapsed="collapsed" />
      </template>

      <template #default="{ collapsed }">
        <UDashboardSearchButton
          :collapsed="collapsed"
          class="bg-transparent ring-(--ui-border)"
        />

        <UNavigationMenu
          :collapsed="collapsed"
          :items="links[0]"
          orientation="vertical"
        />

        <UNavigationMenu
          :collapsed="collapsed"
          :items="links[1]"
          orientation="vertical"
          class="mt-auto"
        />
      </template>

      <template #footer="{ collapsed }">
        <UserMenu :collapsed="collapsed" />
      </template>
    </UDashboardSidebar>

    <UDashboardSearch :groups="groups" />

    <slot />

    <NotificationsSlideover />
  </UDashboardGroup>
</template>
