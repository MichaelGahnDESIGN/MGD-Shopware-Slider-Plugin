// node --test tests/  (Controller Logik, kein DOM)
const test = require('node:test');
const assert = require('node:assert');
const { createController, swipeDirection } = require('../src/Resources/public/js/mgd-slider.js');

test('loop: letzte Slide > weiter springt auf 0', () => {
  const c = createController({ count: 3, loop: true });
  c.goTo(2); c.next();
  assert.strictEqual(c.state.index, 0);
});

test('ohne loop: bleibt am Ende stehen', () => {
  const c = createController({ count: 3, loop: false });
  c.goTo(5);
  assert.strictEqual(c.state.index, 2);
  assert.strictEqual(c.canNext(), false);
});

test('Autoplay laeuft, Pause stoppt', () => {
  const c = createController({ count: 3, autoplay: true });
  assert.strictEqual(c.isPlaying(), true);
  c.togglePause();
  assert.strictEqual(c.isPlaying(), false);
  assert.strictEqual(c.tick(), false);
});

test('Hover und Fokus stoppen Autoplay', () => {
  const c = createController({ count: 3, autoplay: true });
  c.setHover(true);
  assert.strictEqual(c.isPlaying(), false);
  c.setHover(false); c.setFocus(true);
  assert.strictEqual(c.isPlaying(), false);
});

test('prefers-reduced-motion: kein Autoplay', () => {
  const c = createController({ count: 3, autoplay: true, reducedMotion: true });
  assert.strictEqual(c.isPlaying(), false);
});

test('Wischen: nach links = naechste, senkrecht = nichts', () => {
  assert.strictEqual(swipeDirection(-80, 5), 1);
  assert.strictEqual(swipeDirection(80, 5), -1);
  assert.strictEqual(swipeDirection(10, 0), 0);
  assert.strictEqual(swipeDirection(60, 100), 0);
});
