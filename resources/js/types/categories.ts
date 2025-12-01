export interface Category {
    id: number;
    name: string;
    children?: Category[]; // вложенные категории
}
