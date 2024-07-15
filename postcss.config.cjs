/*global module, require*/

module.exports = {
  plugins: [
    require("@fullhuman/postcss-purgecss")({
      content: [
        `./app/Views/**/*.php`,
        `./resources/**/*.js`,
        `./resources/**/*.vue`,
      ],
      defaultExtractor: (content) => {
        const contentWithoutStyleBlocks = content.replace(
          /<style[^]+?<\/style>/gi,
          ""
        );
        return contentWithoutStyleBlocks.match(/[A-Za-z0-9-_/:]*[A-Za-z0-9-_/]+/g) || [];
      },
      safelist:{
        standard: [
          /-(leave|enter|appear)(|-(to|from|active))$/,
          /^(?!(|.*?:)cursor-move).+-move$/,
          /^router-link(|-exact)-active$/,
          /data-v-.*/,
        ],
        deep: [
  
        ],
        greedy: [
          /^modal-/,
        ]
      }
    }),
  ],
};
