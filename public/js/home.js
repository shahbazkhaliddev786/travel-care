document.addEventListener('DOMContentLoaded', () => {
  const initArrowSlider = (rootSelector, containerSelector) => {
    const root = document.querySelector(rootSelector);
    if (!root) return;

    const container = root.querySelector(containerSelector);
    const prevBtn = root.querySelector('.slider-arrow.prev');
    const nextBtn = root.querySelector('.slider-arrow.next');
    if (!container || !prevBtn || !nextBtn) return;

    const getScrollStep = () => {
      const styles = getComputedStyle(container);
      const paddingLeft = parseFloat(styles.paddingLeft) || 0;
      const paddingRight = parseFloat(styles.paddingRight) || 0;
      const visible = container.clientWidth - paddingLeft - paddingRight;
      return Math.max(200, Math.floor(visible * 0.8));
    };

    const scrollByDelta = (delta) => {
      const maxScroll = Math.max(0, container.scrollWidth - container.clientWidth);
      const target = Math.min(Math.max(0, container.scrollLeft + delta), maxScroll);
      container.scrollTo({ left: target, behavior: 'smooth' });
    };

    prevBtn.addEventListener('click', () => scrollByDelta(-getScrollStep()));
    nextBtn.addEventListener('click', () => scrollByDelta(getScrollStep()));
  };

  // Specialties slider
  initArrowSlider('.specialties-slider', '.specialties-container');
  // Top Doctors slider
  initArrowSlider('.top-doctors-slider', '.doctors-grid');
});
