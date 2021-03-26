# Elodin Testimonials

A plugin for listing testimonials with default vanilla styles.

## Sample shortcodes for layouts

For a slider, do something like this:

```
[loop post_type=testimonials layout=testimonial_slider posts_per_page=5]
```

For a grid, do something like this: 

```
[loop post_type=testimonials layout=testimonial_grid posts_per_page=-1]
```

## Sample shortcodes for categories

If you'd like to pull in a particular category, you'll want to do something like this (replacing "termslug" with the slug of your term):

```
[loop post_type=testimonials layout=testimonial_slider posts_per_page=-1 taxonomy=testimonialcategories terms=termslug]
```
