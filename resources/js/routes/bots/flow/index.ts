import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\BotController::update
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
*/
export const update = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/bots/{bot}/flow',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\BotController::update
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
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
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
*/
update.patch = (args: { bot: string | number | { id: string | number } } | [bot: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\BotController::update
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
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
* @see app/Http/Controllers/BotController.php:120
* @route '/bots/{bot}/flow'
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

const flow = {
    update: Object.assign(update, update),
}

export default flow