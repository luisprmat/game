<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps(['games'])
</script>

<template>
    <Head :title="$t('Dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                {{ $t('Dashboard') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 dark:text-gray-200">
                <div
                    class="overflow-hidden bg-white px-6 py-4 shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <Link
                        :href="route('games.store')"
                        method="post"
                        as="button"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
                        >{{ $t('Create Game') }}</Link
                    >

                    <ul class="mt-6 divide-y dark:divide-gray-700">
                        <li
                            v-for="game in games.data"
                            :key="game.id"
                            class="flex items-center justify-between px-2 py-1.5"
                        >
                            <span>{{ game.player_one.name }}</span>
                            <Link
                                :href="route('games.join', game)"
                                method="post"
                                as="button"
                                class="rounded p-2 transition-colors hover:bg-gray-50 dark:hover:bg-gray-900"
                                >{{ $t('Join Game') }}</Link
                            >
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
