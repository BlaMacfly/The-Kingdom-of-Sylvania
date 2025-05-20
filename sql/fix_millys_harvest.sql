-- Fix Milly's Harvest gameobject to make it clickable
UPDATE gameobject_template 
SET Data2 = 1 
WHERE entry = 161557;
