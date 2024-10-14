import i18n from '../../i18n';
import { FontAwesomeIconProps } from '../Icons/FontAwesomeIconProps';
import { QueryBuilderIndexRequest } from '../../Communication/Base/QueryBuilderIndexRequest';
import moment from 'moment';
import I18n from '../../i18n';
import { SortDirection } from '../../Utilities/SortDirection';

export type DateRangeValue = {
  start: string | undefined;
  end: string | undefined;
};

export enum FilterType {
  Search,
  Select,
  RemoteSelect,
  Date,
}

type BaseFilterOption = {
  // Name of the filter to set on request
  filter: string;
  type: FilterType;
};

export type SearchFilterOption = BaseFilterOption & {
  // Placeholder for input box
  placeholder?: string;
  type: FilterType.Search;
};

export type SelectFilterOption<T extends Record<string, unknown>> =
  BaseFilterOption & {
    options?: Array<string|number|boolean>;
    label?: (option: T) => string;
    value?: (option: T) => string|number|boolean;
    type: FilterType.Select;
  };

export type RemoteSelectFilterOptions<T extends Record<string, unknown>> = {
  request: QueryBuilderIndexRequest<T>;
  type: FilterType.RemoteSelect;
};

export enum DateFilterType {
  DateRange,
  TimeRange,
  FromDate,
  UntilDate,
  ExactDate,
  FromTime,
  UntilTime,
  ExactTime,
}

export type DateFilterOption<T extends Record<string, unknown>> =
  BaseFilterOption & {
    transformDate?: (date: Date, entry: T) => Date | string | number;
    type: FilterType.Date;
    startDate?: Date;
    endDate?: Date;
    dateFilterType: DateFilterType;
  };

export type FilterOption<
  T extends Record<string, unknown> = Record<string, unknown>,
> = BaseFilterOption &
  (
    | SearchFilterOption
    | SelectFilterOption<T>
    | RemoteSelectFilterOptions<T>
    | DateFilterOption<T>
  );

export type TableHeader<T extends Record<string, unknown>> = {
  title: string;
  key: string;
  width?: number;
  bodySlot?: string;
  headerSlot?: string;
  filter?: FilterOption<T>;
  sortable?: boolean;
  defaultSortDirection?: SortDirection;
  renderBody?: (entry: T) => string;
};

export type BulkOption<T extends Record<string, unknown>> = {
  title: string;
  onClick: (selected: T[]) => void;
  unselectAfter?: boolean;
  icon?: FontAwesomeIconProps;
  classes?: string;
  confirmation?: true;
  confirmationText?: ((selected: T[]) => string) | string;
};

export const IdHeader: TableHeader<Record<string, unknown & { id: number }>> = {
  key: 'id',
  title: i18n.global.t('components.datatable.id_title'),
  sortable: true,
  filter: {
    type: FilterType.Search,
    filter: 'id',
    placeholder: I18n.global.t('components.datatable.id_search_placeholder'),
  },
};

export const CreatedAtHeader: TableHeader<
  Record<string, unknown> & { created_at: string }
> = {
  key: 'created_at',
  title: i18n.global.t('components.datatable.created_at_title'),
  sortable: true,
  renderBody: entry =>
    moment(entry.created_at).locale(I18n.global.locale).format('lll'),
  filter: {
    type: FilterType.Date,
    filter: 'created_at',
    dateFilterType: DateFilterType.FromDate,
  } as DateFilterOption<Record<string, unknown>>,
};

export const UpdatedAtHeader: TableHeader<
  Record<string, unknown> & { updated_at: string }
> = {
  key: 'updated_at',
  title: i18n.global.t('components.datatable.updated_at_title'),
  sortable: true,
  renderBody: entry =>
    moment(entry.updated_at).locale(I18n.global.locale).format('lll'),
  filter: {
    type: FilterType.Date,
    filter: 'updated_at',
    dateFilterType: DateFilterType.DateRange,
  } as DateFilterOption<Record<string, unknown>>,
};
