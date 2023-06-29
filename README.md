# JetEngine - break listing by yerms.

Allow to break single listing grid into sections separated by terms. Something like this:

![image](https://github.com/ihslimn/jet-engine-break-listing-by-tax/assets/57287929/9b364f84-58af-45bb-971d-5c8a94eb4687)

Plugin works only with Query Builder, so you can break only listings where you get the posts with Query Builder

And last note - plugin doesn't sort posts by terms itself, it only adding breaks based on comparison of posts terms. So you need to sort post by yourself with Query settings

## Setup
- Download and intall plugin,
- Add '--break-by-tax-TAXONOMY_SLUG' (e.g. '--break-by-tax-product_cat') into Query Name in Query builder:
![image](https://github.com/ihslimn/jet-engine-break-listing-by-tax/assets/57287929/42611faa-ac35-405b-93e3-e689cd6305f6)

To sort posts by terms, use SQL query:
```sql
SELECT ID FROM {prefix}posts AS posts 

INNER JOIN {prefix}term_relationships AS term_relationships 
   ON posts.ID = term_relationships.object_id 
INNER JOIN {prefix}term_taxonomy AS term_taxonomy 
   ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id 
INNER JOIN {prefix}terms AS terms 
   ON term_taxonomy.term_id = terms.term_id  

WHERE term_taxonomy.taxonomy = 'product_cat' 

ORDER BY terms.name ASC;
```

Then, use Query Results macro to pull IDs to Post In in Posts Query
![image](https://github.com/ihslimn/jet-engine-break-listing-by-tax/assets/57287929/b94084ad-368b-4be9-99ed-10da7d2096ff)
and set order 'Preserve post ID order given in the \`Post In\` option'
![image](https://github.com/ihslimn/jet-engine-break-listing-by-tax/assets/57287929/0b78066e-a0c3-45be-a9bd-6f476679dcd7)


**Allowed constants:**

- `JET_ENGINE_BREAK_TAX_OPEN_HTML` - by default `<h4 class="jet-engine-break-listing" style="width:100%; flex: 0 0 100%;">` - opening HTML markup for term name. Please note - "style="width:100%; flex: 0 0 100%;" is important for multicolumn layout
- `JET_ENGINE_BREAK_TAX_CLOSE_HTML` - by default `</h4>` - closing HTML markup
