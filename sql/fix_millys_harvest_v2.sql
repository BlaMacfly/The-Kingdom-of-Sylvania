-- Fix Milly's Harvest gameobject to make it fully interactive
UPDATE gameobject_template 
SET Data6 = 1 
WHERE entry = 161557;
