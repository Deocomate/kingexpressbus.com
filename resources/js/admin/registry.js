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
        matchesInTree(root, selector).forEach((el) => {
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
        matchesInTree(root, selector).forEach((el) => {
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

/**
 * Elements matching `selector` within root, INCLUDING root itself.
 * `querySelectorAll` alone only searches descendants, so a root element
 * that itself carries the matched attribute (e.g. the `[data-table]` div
 * passed straight into initTree/destroyTree after a partial HTML swap)
 * would otherwise never get its controller re-attached.
 * @param {Element} root
 * @param {string} selector
 * @returns {Element[]}
 */
function matchesInTree(root, selector) {
    const els = Array.from(root.querySelectorAll(selector));
    if (root.matches?.(selector)) {
        els.unshift(root);
    }
    return els;
}
