/**
 * Component registry: manages init/destroy lifecycle for DOM-enhancement controllers.
 * Call initTree(root) after any HTML swap; call destroyTree(root) before removing HTML.
 */

/** @type {Map<string, {init: (el: Element) => void, destroy: (el: Element) => void}>} */
const controllers = new Map();

/**
 * Register a controller for elements matching the given CSS selector.
 * @param {string} selector
 * @param {{ init(el: Element): void, destroy(el: Element): void }} controller
 */
export function register(selector, controller) {
    controllers.set(selector, controller);
}

/**
 * Initialize all registered controllers within the given root element.
 * Also calls Alpine.initTree(root) if Alpine is available.
 * @param {Element} [root=document.body]
 */
export function initTree(root = document.body) {
    controllers.forEach(({ init }, selector) => {
        root.querySelectorAll(selector).forEach((el) => {
            try {
                init(el);
            } catch (err) {
                console.error(`[registry] init error for "${selector}"`, err);
            }
        });
    });

    if (window.Alpine?.initTree) {
        window.Alpine.initTree(root);
    }
}

/**
 * Destroy all registered controllers within the given root element.
 * @param {Element} [root=document.body]
 */
export function destroyTree(root = document.body) {
    controllers.forEach(({ destroy }, selector) => {
        root.querySelectorAll(selector).forEach((el) => {
            try {
                if (destroy) destroy(el);
            } catch (err) {
                console.error(`[registry] destroy error for "${selector}"`, err);
            }
        });
    });

    if (window.Alpine?.destroyTree) {
        window.Alpine.destroyTree(root);
    }
}
