import jQuery from 'jquery';

// toastr's UMD build reads window.jQuery synchronously at module-eval time.
// This must be its own module, imported before `toastr`, so the assignment
// below finishes (this module's import subgraph fully evaluates first)
// before toastr's factory runs — a plain top-level statement order inside
// a single file would NOT be enough under ESM's import-then-evaluate model.
window.$ = jQuery;
window.jQuery = jQuery;

export default jQuery;
