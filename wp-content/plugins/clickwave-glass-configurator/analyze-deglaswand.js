import { chromium } from 'playwright';
import fs from 'fs';

(async () => {
  const browser = await chromium.launch({ headless: false });
  const page = await browser.newPage();

  // Set viewport to desktop size
  await page.setViewportSize({ width: 1920, height: 1080 });

  console.log('Navigating to https://deglaswand.nl/...');
  await page.goto('https://deglaswand.nl/', { waitUntil: 'networkidle' });

  // Wait a bit for any animations
  await page.waitForTimeout(2000);

  // ===========================
  // ANALYZE DARK BLUE SECTION
  // ===========================
  console.log('\n=== SEARCHING FOR DARK BLUE SECTION WITH "een heldere kijk" ===');

  // Find the section with "een heldere kijk" text
  const darkSection = await page.locator('text="een heldere kijk"').first();

  if (await darkSection.count() > 0) {
    console.log('Found "een heldere kijk" text, scrolling to it...');
    await darkSection.scrollIntoViewIfNeeded();
    await page.waitForTimeout(1000);

    // Take screenshot of the dark section
    await page.screenshot({
      path: '/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/dark-section-screenshot.png',
      fullPage: false
    });
    console.log('Screenshot saved: dark-section-screenshot.png');

    // Get the parent section element using JavaScript
    const sectionElement = await darkSection.evaluateHandle((el) => {
      // Try to find a parent section or container div
      let parent = el.closest('section, [class*="section"], [class*="block"], .wp-block-group');
      if (!parent) {
        // If no semantic parent, go up a few levels
        parent = el.parentElement?.parentElement || el.parentElement;
      }
      return parent;
    });

    // Extract design tokens for dark section
    const darkSectionTokens = await sectionElement.evaluate((el) => {
      const computed = window.getComputedStyle(el);
      const rect = el.getBoundingClientRect();

      return {
        element: el.tagName,
        className: el.className,
        dimensions: {
          width: rect.width,
          height: rect.height
        },
        background: {
          backgroundColor: computed.backgroundColor,
          backgroundImage: computed.backgroundImage,
          backgroundSize: computed.backgroundSize,
          backgroundPosition: computed.backgroundPosition
        },
        spacing: {
          paddingTop: computed.paddingTop,
          paddingRight: computed.paddingRight,
          paddingBottom: computed.paddingBottom,
          paddingLeft: computed.paddingLeft,
          marginTop: computed.marginTop,
          marginBottom: computed.marginBottom
        },
        layout: {
          display: computed.display,
          flexDirection: computed.flexDirection,
          justifyContent: computed.justifyContent,
          alignItems: computed.alignItems,
          gap: computed.gap
        }
      };
    });

    // Get text elements inside the dark section
    const textTokens = await sectionElement.evaluate((el) => {
      const textElements = el.querySelectorAll('h1, h2, h3, h4, h5, h6, p, a, span');
      const tokens = [];

      for (let i = 0; i < Math.min(textElements.length, 10); i++) {
        const textEl = textElements[i];
        const computed = window.getComputedStyle(textEl);
        tokens.push({
          tagName: textEl.tagName,
          text: textEl.textContent.trim().substring(0, 100),
          className: textEl.className,
          typography: {
            fontFamily: computed.fontFamily,
            fontSize: computed.fontSize,
            fontWeight: computed.fontWeight,
            lineHeight: computed.lineHeight,
            letterSpacing: computed.letterSpacing,
            textTransform: computed.textTransform,
            color: computed.color
          },
          spacing: {
            marginTop: computed.marginTop,
            marginBottom: computed.marginBottom,
            paddingTop: computed.paddingTop,
            paddingBottom: computed.paddingBottom
          }
        });
      }
      return tokens;
    });

    console.log('\n--- Dark Section Design Tokens ---');
    console.log(JSON.stringify({ section: darkSectionTokens, textElements: textTokens }, null, 2));

    // Save to file
    fs.writeFileSync(
      '/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/dark-section-tokens.json',
      JSON.stringify({ section: darkSectionTokens, textElements: textTokens }, null, 2)
    );
  } else {
    console.log('Could not find "een heldere kijk" text on the page');
  }

  // ===========================
  // ANALYZE FOOTER
  // ===========================
  console.log('\n=== SCROLLING TO FOOTER ===');

  // Scroll to footer
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await page.waitForTimeout(1000);

  // Take screenshot of footer
  await page.screenshot({
    path: '/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/footer-screenshot.png',
    fullPage: false
  });
  console.log('Screenshot saved: footer-screenshot.png');

  // Find footer element
  const footer = page.locator('footer').first();

  if (await footer.count() > 0) {
    // Extract design tokens for footer
    const footerTokens = await footer.evaluate((el) => {
      const computed = window.getComputedStyle(el);
      const rect = el.getBoundingClientRect();

      return {
        element: el.tagName,
        className: el.className,
        dimensions: {
          width: rect.width,
          height: rect.height
        },
        background: {
          backgroundColor: computed.backgroundColor,
          backgroundImage: computed.backgroundImage
        },
        spacing: {
          paddingTop: computed.paddingTop,
          paddingRight: computed.paddingRight,
          paddingBottom: computed.paddingBottom,
          paddingLeft: computed.paddingLeft
        },
        layout: {
          display: computed.display,
          flexDirection: computed.flexDirection,
          justifyContent: computed.justifyContent,
          alignItems: computed.alignItems,
          gap: computed.gap,
          gridTemplateColumns: computed.gridTemplateColumns
        }
      };
    });

    console.log('\n--- Footer Design Tokens ---');
    console.log(JSON.stringify(footerTokens, null, 2));

    // Get logo in footer
    const footerLogo = footer.locator('img, svg').first();
    let logoTokens = null;

    if (await footerLogo.count() > 0) {
      logoTokens = await footerLogo.evaluate((el) => {
        const computed = window.getComputedStyle(el);
        const rect = el.getBoundingClientRect();

        return {
          element: el.tagName,
          src: el.src || el.outerHTML.substring(0, 200),
          alt: el.alt || '',
          dimensions: {
            width: rect.width,
            height: rect.height,
            computedWidth: computed.width,
            computedHeight: computed.height
          },
          spacing: {
            marginTop: computed.marginTop,
            marginRight: computed.marginRight,
            marginBottom: computed.marginBottom,
            marginLeft: computed.marginLeft
          }
        };
      });
      console.log('\n--- Footer Logo Tokens ---');
      console.log(JSON.stringify(logoTokens, null, 2));
    }

    // Get all columns/sections in footer
    const footerColumns = await footer.locator('> div, > nav, > section').all();
    const columnTokens = [];

    for (let i = 0; i < footerColumns.length; i++) {
      const columnToken = await footerColumns[i].evaluate((el) => {
        const computed = window.getComputedStyle(el);
        const rect = el.getBoundingClientRect();

        return {
          element: el.tagName,
          className: el.className,
          dimensions: {
            width: rect.width,
            height: rect.height
          },
          layout: {
            display: computed.display,
            flexDirection: computed.flexDirection,
            justifyContent: computed.justifyContent,
            alignItems: computed.alignItems,
            gap: computed.gap,
            flex: computed.flex
          },
          spacing: {
            padding: computed.padding,
            margin: computed.margin
          }
        };
      });
      columnTokens.push(columnToken);
    }

    console.log('\n--- Footer Column Tokens ---');
    console.log(JSON.stringify(columnTokens, null, 2));

    // Get typography samples from footer
    const footerTextElements = await footer.locator('h1, h2, h3, h4, h5, h6, p, a, span, li').all();
    const footerTypography = [];

    for (let i = 0; i < Math.min(footerTextElements.length, 15); i++) {
      const textToken = await footerTextElements[i].evaluate((el) => {
        const computed = window.getComputedStyle(el);
        return {
          tagName: el.tagName,
          text: el.textContent.trim().substring(0, 50),
          className: el.className,
          typography: {
            fontFamily: computed.fontFamily,
            fontSize: computed.fontSize,
            fontWeight: computed.fontWeight,
            lineHeight: computed.lineHeight,
            letterSpacing: computed.letterSpacing,
            textTransform: computed.textTransform,
            color: computed.color
          }
        };
      });
      footerTypography.push(textToken);
    }

    console.log('\n--- Footer Typography Tokens ---');
    console.log(JSON.stringify(footerTypography, null, 2));

    // Save all footer data to file
    const footerData = {
      footer: footerTokens,
      logo: logoTokens,
      columns: columnTokens,
      typography: footerTypography
    };

    fs.writeFileSync(
      '/Users/roy/Local Sites/deglaswand/app/public/wp-content/plugins/clickwave-glass-configurator/footer-tokens.json',
      JSON.stringify(footerData, null, 2)
    );

    console.log('\n=== ANALYSIS COMPLETE ===');
    console.log('Tokens saved to footer-tokens.json and dark-section-tokens.json');
  } else {
    console.log('Footer element not found!');
  }

  // Keep browser open for manual inspection
  console.log('\nBrowser will stay open for 30 seconds for manual inspection...');
  await page.waitForTimeout(30000);

  await browser.close();
})();
