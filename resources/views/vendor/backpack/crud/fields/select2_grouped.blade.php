// -- /resoruces/views/vendor/backpack/crud/fields/select2_grouped.blade.php
// -- Beginning and end of standard select2 option clipped...
<select
        name="{{ $field['name'] }}"
        @include('crud::inc.field_attributes', ['default_class' =>  'form-control select2'])
        >

            @if ($entity_model::isColumnNullable($field['name']))
                <option value="">-</option>
            @endif
             
            @if (isset($field['model']) && isset($field['group_entity']))
                @foreach ($field['model']::with($field['group_entity'])->get() as $connected_entity_entry)
                    <optgroup label="{{ $connected_entity_entry->{$field['group_label_attribute']} }}">
                        @foreach ($connected_entity_entry->{$field['group_entity']} as $subconnected_entity_entry)
                            <option value="{{ $subconnected_entity_entry->getKey() }}"
                                @if ( ( old($field['name']) && old($field['name']) == $subconnected_entity_entry->getKey() ) || (isset($field['value']) && $subconnected_entity_entry->getKey()==$field['value']))
                                     selected
                                @endif
                            >{{ $subconnected_entity_entry->{$field['attribute']} }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            @endif
    </select>