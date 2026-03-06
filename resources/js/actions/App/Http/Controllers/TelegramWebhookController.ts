import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TelegramWebhookController::__invoke
* @see app/Http/Controllers/TelegramWebhookController.php:19
* @route '/telegram/webhook'
*/
const TelegramWebhookController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: TelegramWebhookController.url(options),
    method: 'post',
})

TelegramWebhookController.definition = {
    methods: ["post"],
    url: '/telegram/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TelegramWebhookController::__invoke
* @see app/Http/Controllers/TelegramWebhookController.php:19
* @route '/telegram/webhook'
*/
TelegramWebhookController.url = (options?: RouteQueryOptions) => {
    return TelegramWebhookController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TelegramWebhookController::__invoke
* @see app/Http/Controllers/TelegramWebhookController.php:19
* @route '/telegram/webhook'
*/
TelegramWebhookController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: TelegramWebhookController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TelegramWebhookController::__invoke
* @see app/Http/Controllers/TelegramWebhookController.php:19
* @route '/telegram/webhook'
*/
const TelegramWebhookControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: TelegramWebhookController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TelegramWebhookController::__invoke
* @see app/Http/Controllers/TelegramWebhookController.php:19
* @route '/telegram/webhook'
*/
TelegramWebhookControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: TelegramWebhookController.url(options),
    method: 'post',
})

TelegramWebhookController.form = TelegramWebhookControllerForm

export default TelegramWebhookController