import TelegramWebhookController from './TelegramWebhookController'
import BotDashboardController from './BotDashboardController'
import BotController from './BotController'
import Settings from './Settings'

const Controllers = {
    TelegramWebhookController: Object.assign(TelegramWebhookController, TelegramWebhookController),
    BotDashboardController: Object.assign(BotDashboardController, BotDashboardController),
    BotController: Object.assign(BotController, BotController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers