<script lang="ts">
    import { Form, Link, page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { dashboard } from '@/routes';
    import type { BreadcrumbItem } from '@/types';

    type Template = {
        key: string;
        name: string;
        description: string;
    };

    type Bot = {
        id: number;
        name: string;
        template_key: string;
        telegram_bot_username: string | null;
        welcome_message: string | null;
        support_username: string | null;
        products_link: string | null;
        button_text: string | null;
        settings_json: Record<string, unknown> | null;
        flow_json: unknown;
        status: string;
        webhook_status: string;
        created_at: string;
        subscribers_count: number;
        joined_today_count: number;
    };

    type Subscriber = {
        id: number;
        name: string | null;
        username: string | null;
        joined_at: string | null;
    };

    let {
        templates,
        bots,
        selectedBot,
        selectedSubscribers,
        status,
    }: {
        templates: Template[];
        bots: Bot[];
        selectedBot: Bot | null;
        selectedSubscribers: Subscriber[];
        status?: string | null;
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ];

    const flashStatus = $derived((status || $page.props.flash?.status || null) as string | null);

    const statusVariant = (botStatus: string) =>
        botStatus === 'active' ? 'default' : 'secondary';

    const templateLabel = (key: string) =>
        templates.find((template) => template.key === key)?.name || key;

    const formatDate = (date: string | null) => {
        if (!date) return '-';

        return new Date(date).toLocaleDateString();
    };

    const formatFlow = (flow: unknown) => JSON.stringify(flow ?? [], null, 2);

    const settingValue = (settings: Record<string, unknown> | null, key: string) => {
        const value = settings?.[key];
        return typeof value === 'string' ? value : '';
    };
</script>

<AppHead title="Dashboard" />

<AppLayout {breadcrumbs}>
    <div class="flex flex-col gap-6 p-4 md:p-6">
        <section class="rounded-xl border bg-gradient-to-r from-orange-50 to-emerald-50 p-5 dark:from-orange-950/20 dark:to-emerald-950/20">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">MVP Flow</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight">Create bot, customize, publish in under 2 minutes</h1>
            <p class="mt-2 text-sm text-muted-foreground">
                1. Paste Telegram token. 2. Choose template. 3. Publish. Then manage users and broadcasts from one dashboard.
            </p>
        </section>

        {#if flashStatus}
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950/30 dark:text-emerald-300">
                {flashStatus}
            </div>
        {/if}

        <section class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border bg-card p-5">
                <h2 class="text-lg font-semibold">Create Telegram Bot</h2>
                <p class="mt-1 text-sm text-muted-foreground">Paste your bot token from BotFather and pick a starter template.</p>

                <div class="mt-3 text-sm">
                    <a
                        href="https://t.me/BotFather"
                        target="_blank"
                        rel="noreferrer"
                        class="font-medium text-primary underline underline-offset-2"
                    >
                        Open Telegram BotFather
                    </a>
                </div>

                <Form action="/bots" method="post" class="mt-4 space-y-4">
                    {#snippet children({ errors, processing })}
                        <div class="grid gap-2">
                            <Label for="name">Bot Name</Label>
                            <Input id="name" name="name" required placeholder="Axum Store Bot" />
                            {#if errors.name}<p class="text-xs text-destructive">{errors.name}</p>{/if}
                        </div>

                        <div class="grid gap-2">
                            <Label for="bot_token">Bot Token</Label>
                            <Input id="bot_token" name="bot_token" required placeholder="123456:AA..." />
                            {#if errors.bot_token}<p class="text-xs text-destructive">{errors.bot_token}</p>{/if}
                        </div>

                        <div class="grid gap-2">
                            <Label for="telegram_bot_username">Bot Username (optional)</Label>
                            <Input id="telegram_bot_username" name="telegram_bot_username" placeholder="axum_store_bot" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="template_key">Template</Label>
                            <select
                                id="template_key"
                                name="template_key"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm"
                            >
                                {#each templates as template (template.key)}
                                    <option value={template.key}>{template.name}</option>
                                {/each}
                            </select>
                        </div>

                        <Button type="submit" disabled={processing}>Continue</Button>
                    {/snippet}
                </Form>
            </div>

            <div class="rounded-xl border bg-card p-5">
                <h2 class="text-lg font-semibold">My Bots</h2>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-muted-foreground">
                            <tr class="border-b">
                                <th class="px-2 py-2 font-medium">Bot</th>
                                <th class="px-2 py-2 font-medium">Users</th>
                                <th class="px-2 py-2 font-medium">Status</th>
                                <th class="px-2 py-2 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if bots.length === 0}
                                <tr>
                                    <td colspan="4" class="px-2 py-6 text-muted-foreground">No bots yet.</td>
                                </tr>
                            {:else}
                                {#each bots as bot (bot.id)}
                                    <tr class="border-b last:border-b-0">
                                        <td class="px-2 py-3">
                                            <div class="font-medium">{bot.name}</div>
                                            <div class="text-xs text-muted-foreground">{templateLabel(bot.template_key)}</div>
                                            <Link href={`/dashboard?bot=${bot.id}`} class="text-xs text-primary underline underline-offset-2">Open dashboard</Link>
                                        </td>
                                        <td class="px-2 py-3">{bot.subscribers_count}</td>
                                        <td class="px-2 py-3">
                                            <Badge variant={statusVariant(bot.status)}>{bot.status}</Badge>
                                        </td>
                                        <td class="px-2 py-3">
                                            <div class="flex flex-wrap gap-2 text-xs">
                                                <Link href={`/dashboard?bot=${bot.id}`} class="text-primary underline underline-offset-2">Open</Link>
                                                <Link href={`/dashboard?bot=${bot.id}#edit-content`} class="text-primary underline underline-offset-2">Edit</Link>
                                                <Link href={`/dashboard?bot=${bot.id}#broadcast`} class="text-primary underline underline-offset-2">Broadcast</Link>
                                                <Link href={`/dashboard?bot=${bot.id}#flow-editor`} class="text-primary underline underline-offset-2">Flow</Link>
                                                <Link href={`/dashboard?bot=${bot.id}#analytics`} class="text-primary underline underline-offset-2">Analytics</Link>
                                            </div>
                                        </td>
                                    </tr>
                                {/each}
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {#if selectedBot}
            <section class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Users</p>
                    <p class="mt-2 text-3xl font-semibold">{selectedBot.subscribers_count}</p>
                </div>
                <div class="rounded-xl border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Joined Today</p>
                    <p class="mt-2 text-3xl font-semibold">{selectedBot.joined_today_count}</p>
                </div>
                <div class="rounded-xl border bg-card p-4">
                    <p class="text-sm text-muted-foreground">Webhook</p>
                    <p class="mt-2 text-3xl font-semibold capitalize">{selectedBot.webhook_status.replace('_', ' ')}</p>
                </div>
            </section>

            <section class="grid gap-4 xl:grid-cols-2">
                <div id="edit-content" class="rounded-xl border bg-card p-5">
                    <h3 class="text-lg font-semibold">Edit Bot Content</h3>
                    <p class="mt-1 text-sm text-muted-foreground">Update fields and regenerate the active flow.</p>

                    <Form action={`/bots/${selectedBot.id}`} method="patch" class="mt-4 space-y-4">
                        {#snippet children({ errors, processing })}
                            <div class="grid gap-2">
                                <Label for="edit_name">Bot Name</Label>
                                <Input id="edit_name" name="name" value={selectedBot.name} required />
                                {#if errors.name}<p class="text-xs text-destructive">{errors.name}</p>{/if}
                            </div>

                            <div class="grid gap-2">
                                <Label for="edit_username">Bot Username</Label>
                                <Input
                                    id="edit_username"
                                    name="telegram_bot_username"
                                    value={selectedBot.telegram_bot_username || ''}
                                    placeholder="axum_store_bot"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="edit_welcome">Welcome Message</Label>
                                <textarea
                                    id="edit_welcome"
                                    name="welcome_message"
                                    rows="3"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                >{selectedBot.welcome_message || ''}</textarea>
                            </div>

                            <div class="grid gap-2 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="edit_button_text">Button Text</Label>
                                    <Input
                                        id="edit_button_text"
                                        name="button_text"
                                        value={selectedBot.button_text || ''}
                                    />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="edit_products_link">Products Link</Label>
                                    <Input
                                        id="edit_products_link"
                                        name="products_link"
                                        value={selectedBot.products_link || ''}
                                        placeholder="https://example.com/products"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-2 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="edit_support_username">Support Username</Label>
                                    <Input
                                        id="edit_support_username"
                                        name="support_username"
                                        value={selectedBot.support_username || ''}
                                        placeholder="@support"
                                    />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="edit_support_button_text">Support Button Text</Label>
                                    <Input
                                        id="edit_support_button_text"
                                        name="support_button_text"
                                        value={settingValue(selectedBot.settings_json, 'support_button_text')}
                                        placeholder="Support"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-2 md:grid-cols-2">
                                <div class="grid gap-2">
                                        <Label for="edit_faq_answer">FAQ Answer</Label>
                                        <Input
                                            id="edit_faq_answer"
                                            name="faq_answer"
                                            value={settingValue(selectedBot.settings_json, 'faq_answer')}
                                        />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="edit_lead_name">Lead Prompt (Name)</Label>
                                        <Input
                                            id="edit_lead_name"
                                            name="lead_prompt_name"
                                            value={settingValue(selectedBot.settings_json, 'lead_prompt_name')}
                                        />
                                    </div>
                                </div>

                            <div class="grid gap-2">
                                <Label for="edit_lead_phone">Lead Prompt (Phone)</Label>
                                <Input
                                    id="edit_lead_phone"
                                    name="lead_prompt_phone"
                                    value={settingValue(selectedBot.settings_json, 'lead_prompt_phone')}
                                />
                            </div>

                            <Button type="submit" disabled={processing}>Save Content</Button>
                        {/snippet}
                    </Form>
                </div>

                <div class="space-y-4">
                    <div id="broadcast" class="rounded-xl border bg-card p-5">
                        <h3 class="text-lg font-semibold">Broadcast Message</h3>
                        <p class="mt-1 text-sm text-muted-foreground">Send a broadcast to your audience segments.</p>

                        <Form action={`/bots/${selectedBot.id}/broadcast`} method="post" class="mt-4 space-y-4">
                            {#snippet children({ errors, processing })}
                                <div class="grid gap-2">
                                    <Label for="broadcast_message">Message</Label>
                                    <textarea
                                        id="broadcast_message"
                                        name="message"
                                        rows="4"
                                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                        placeholder="Big sale today!"
                                    ></textarea>
                                    {#if errors.message}<p class="text-xs text-destructive">{errors.message}</p>{/if}
                                </div>

                                <div class="grid gap-2 md:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="broadcast_image_url">Image URL (optional)</Label>
                                        <Input id="broadcast_image_url" name="image_url" placeholder="https://..." />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="broadcast_audience">Send To</Label>
                                        <select
                                            id="broadcast_audience"
                                            name="audience"
                                            class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm"
                                        >
                                            <option value="all">All users</option>
                                            <option value="last_24_hours">Last 24 hours</option>
                                            <option value="last_7_days">Last 7 days</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid gap-2 md:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="broadcast_button_text">Button Text (optional)</Label>
                                        <Input id="broadcast_button_text" name="button_text" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="broadcast_button_url">Button URL (optional)</Label>
                                        <Input id="broadcast_button_url" name="button_url" placeholder="https://..." />
                                    </div>
                                </div>

                                <Button type="submit" disabled={processing}>Send Broadcast</Button>
                            {/snippet}
                        </Form>
                    </div>

                    <div class="rounded-xl border bg-card p-5">
                        <h3 class="text-lg font-semibold">Bot Settings</h3>
                        <div class="mt-3 space-y-2 text-sm">
                            <p><span class="text-muted-foreground">Status:</span> <span class="capitalize">{selectedBot.status}</span></p>
                            <p><span class="text-muted-foreground">Webhook:</span> <span class="capitalize">{selectedBot.webhook_status.replace('_', ' ')}</span></p>
                            <p><span class="text-muted-foreground">Username:</span> {selectedBot.telegram_bot_username || '-'}</p>
                            {#if selectedBot.telegram_bot_username}
                                <p>
                                    <a
                                        href={`https://t.me/${selectedBot.telegram_bot_username}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        class="font-medium text-primary underline underline-offset-2"
                                    >Open Bot</a>
                                </p>
                            {/if}
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <Form action={`/bots/${selectedBot.id}/publish`} method="post">
                                {#snippet children({ processing })}
                                    <Button type="submit" disabled={processing}>Publish Bot</Button>
                                {/snippet}
                            </Form>

                            <Form action={`/bots/${selectedBot.id}`} method="delete">
                                {#snippet children({ processing })}
                                    <Button type="submit" variant="destructive" disabled={processing}>Delete Bot</Button>
                                {/snippet}
                            </Form>
                        </div>
                    </div>
                </div>
            </section>

            <section id="flow-editor" class="rounded-xl border bg-card p-5">
                <h3 class="text-lg font-semibold">Flow Editor</h3>
                <p class="mt-1 text-sm text-muted-foreground">Edit and save the bot flow JSON directly.</p>

                <Form action={`/bots/${selectedBot.id}/flow`} method="patch" class="mt-4 space-y-4">
                    {#snippet children({ errors, processing })}
                        <div class="grid gap-2">
                            <Label for="flow_json">Flow JSON</Label>
                            <textarea
                                id="flow_json"
                                name="flow_json"
                                rows="18"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 font-mono text-xs"
                            >{formatFlow(selectedBot.flow_json)}</textarea>
                            {#if errors.flow_json}<p class="text-xs text-destructive">{errors.flow_json}</p>{/if}
                        </div>
                        <Button type="submit" disabled={processing}>Save Flow</Button>
                    {/snippet}
                </Form>
            </section>

            <section id="analytics" class="rounded-xl border bg-card p-5">
                <h3 class="text-lg font-semibold">Users</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-muted-foreground">
                            <tr class="border-b">
                                <th class="px-2 py-2 font-medium">Name</th>
                                <th class="px-2 py-2 font-medium">Username</th>
                                <th class="px-2 py-2 font-medium">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            {#if selectedSubscribers.length === 0}
                                <tr>
                                    <td colspan="3" class="px-2 py-6 text-muted-foreground">No users yet.</td>
                                </tr>
                            {:else}
                                {#each selectedSubscribers as subscriber (subscriber.id)}
                                    <tr class="border-b last:border-b-0">
                                        <td class="px-2 py-3">{subscriber.name || 'Telegram User'}</td>
                                        <td class="px-2 py-3">{subscriber.username ? `@${subscriber.username}` : '-'}</td>
                                        <td class="px-2 py-3">{formatDate(subscriber.joined_at)}</td>
                                    </tr>
                                {/each}
                            {/if}
                        </tbody>
                    </table>
                </div>
            </section>
        {:else}
            <section class="rounded-xl border border-dashed bg-card p-8 text-center">
                <p class="text-lg font-medium">Create your first bot to unlock bot dashboard, users, and broadcasts.</p>
            </section>
        {/if}
    </div>
</AppLayout>
