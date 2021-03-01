import { configure } from '@storybook/react';


function loadStories() {
  // automatically import all files ending in *.stories.ts
  // require.context('../stories', true, /.stories.tsx$/);
        require('../src/storybook/index.stories.tsx');
}

configure(loadStories, module);