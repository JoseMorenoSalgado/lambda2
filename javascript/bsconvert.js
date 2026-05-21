document.addEventListener('DOMContentLoaded', function () {
  function convertDataAttributes(el) {
    [...el.attributes].forEach(attr => {
      const name = attr.name;
      const value = attr.value;
      const allowed = ['data-toggle', 'data-target', 'data-parent'];
      if (
        name.startsWith('data-') &&
        !name.startsWith('data-bs-') &&
        (
          allowed.includes(name) ||
          el.closest('.accordion') ||
          el.closest('.nav.nav-tabs') ||
          el.closest('.nav.nav-pills')
        )
      ) {
        const newName = name.replace(/^data-/, 'data-bs-');
        el.setAttribute(newName, value);
        el.removeAttribute(name);
      }
    });
  }

  function process(root = document) {
    root.querySelectorAll('.accordion *').forEach(convertDataAttributes);
    root.querySelectorAll('[data-toggle="collapse"]').forEach(convertDataAttributes);
    root.querySelectorAll('.nav.nav-tabs [data-toggle], .nav.nav-pills [data-toggle]').forEach(convertDataAttributes);
  }

  process();

  const observer = new MutationObserver(mutations => {
    mutations.forEach(mutation => {
      mutation.addedNodes.forEach(node => {
        if (node.nodeType === 1) {
          process(node);
        }
      });
    });
  });

  observer.observe(document.body, { childList: true, subtree: true });
});
