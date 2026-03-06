import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BotController::store
* @see app/Http/Controllers/BotController.php:19
* @route '/bots'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/bots',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BotController::store
* @see app/Http/Controllers/BotController.php:19
* @route '/bots'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BotController::store
* @see app/Http/Controllers/BotController.php:19
* @route '/bots'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::store
* @see app/Http/Controllers/BotController.php:19
* @route '/bots'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::store
* @see app/Http/Controllers/BotController.php:19
* @route '/bots'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\BotController::update
* @see app/Http/Controllers/BotController.php:61
* @route '/bots/{bot}'
*/
export const update = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/bots/{bot}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BotController::update
* @see app/Http/Controllers/BotController.php:61
* @route '/bots/{bot}'
*/
update.url = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bot: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { bot: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            bot: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        bot: typeof args.bot === 'object'
        ? args.bot.id
        : args.bot,
    }

    return update.definition.url
            .replace('{bot}', parsedArgs.bot.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BotController::update
* @see app/Http/Controllers/BotController.php:61
* @route '/bots/{bot}'
*/
update.patch = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\BotController::update
* @see app/Http/Controllers/BotController.php:61
* @route '/bots/{bot}'
*/
const updateForm = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::update
* @see app/Http/Controllers/BotController.php:61
* @route '/bots/{bot}'
*/
updateForm.patch = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\BotController::updateFlow
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
*/
export const updateFlow = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateFlow.url(args, options),
    method: 'patch',
})

updateFlow.definition = {
    methods: ["patch"],
    url: '/bots/{bot}/flow',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BotController::updateFlow
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
*/
updateFlow.url = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bot: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { bot: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            bot: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        bot: typeof args.bot === 'object'
        ? args.bot.id
        : args.bot,
    }

    return updateFlow.definition.url
            .replace('{bot}', parsedArgs.bot.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BotController::updateFlow
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
*/
updateFlow.patch = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateFlow.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\BotController::updateFlow
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
*/
const updateFlowForm = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateFlow.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::updateFlow
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
*/
updateFlowForm.patch = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateFlow.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateFlow.form = updateFlowForm

/**
* @see \App\Http\Controllers\BotController::publish
* @see app/Http/Controllers/BotController.php:159
* @route '/bots/{bot}/publish'
*/
export const publish = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publish.url(args, options),
    method: 'post',
})

publish.definition = {
    methods: ["post"],
    url: '/bots/{bot}/publish',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BotController::publish
* @see app/Http/Controllers/BotController.php:159
* @route '/bots/{bot}/publish'
*/
publish.url = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bot: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { bot: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            bot: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        bot: typeof args.bot === 'object'
        ? args.bot.id
        : args.bot,
    }

    return publish.definition.url
            .replace('{bot}', parsedArgs.bot.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BotController::publish
* @see app/Http/Controllers/BotController.php:159
* @route '/bots/{bot}/publish'
*/
publish.post = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publish.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::publish
* @see app/Http/Controllers/BotController.php:159
* @route '/bots/{bot}/publish'
*/
const publishForm = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: publish.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::publish
* @see app/Http/Controllers/BotController.php:159
* @route '/bots/{bot}/publish'
*/
publishForm.post = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: publish.url(args, options),
    method: 'post',
})

publish.form = publishForm

/**
* @see \App\Http\Controllers\BotController::broadcast
* @see app/Http/Controllers/BotController.php:212
* @route '/bots/{bot}/broadcast'
*/
export const broadcast = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: broadcast.url(args, options),
    method: 'post',
})

broadcast.definition = {
    methods: ["post"],
    url: '/bots/{bot}/broadcast',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BotController::broadcast
* @see app/Http/Controllers/BotController.php:212
* @route '/bots/{bot}/broadcast'
*/
broadcast.url = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bot: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { bot: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            bot: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        bot: typeof args.bot === 'object'
        ? args.bot.id
        : args.bot,
    }

    return broadcast.definition.url
            .replace('{bot}', parsedArgs.bot.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BotController::broadcast
* @see app/Http/Controllers/BotController.php:212
* @route '/bots/{bot}/broadcast'
*/
broadcast.post = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: broadcast.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::broadcast
* @see app/Http/Controllers/BotController.php:212
* @route '/bots/{bot}/broadcast'
*/
const broadcastForm = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: broadcast.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::broadcast
* @see app/Http/Controllers/BotController.php:212
* @route '/bots/{bot}/broadcast'
*/
broadcastForm.post = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: broadcast.url(args, options),
    method: 'post',
})

broadcast.form = broadcastForm

/**
* @see \App\Http\Controllers\BotController::destroy
* @see app/Http/Controllers/BotController.php:260
* @route '/bots/{bot}'
*/
export const destroy = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/bots/{bot}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BotController::destroy
* @see app/Http/Controllers/BotController.php:260
* @route '/bots/{bot}'
*/
destroy.url = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { bot: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { bot: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            bot: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        bot: typeof args.bot === 'object'
        ? args.bot.id
        : args.bot,
    }

    return destroy.definition.url
            .replace('{bot}', parsedArgs.bot.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BotController::destroy
* @see app/Http/Controllers/BotController.php:260
* @route '/bots/{bot}'
*/
destroy.delete = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BotController::destroy
* @see app/Http/Controllers/BotController.php:260
* @route '/bots/{bot}'
*/
const destroyForm = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BotController::destroy
* @see app/Http/Controllers/BotController.php:260
* @route '/bots/{bot}'
*/
destroyForm.delete = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const BotController = { store, update, updateFlow, publish, broadcast, destroy }

export default BotController