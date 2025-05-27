<script setup lang="ts">
definePageMeta({
  middleware: ["auth", "role"],
  allowedRoles: ["admin", "blood_bank"],
});
// ... existing code ...
</script>
// ... existing code ...
