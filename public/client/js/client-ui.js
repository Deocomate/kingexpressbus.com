(function () {
    function normalize(value) {
        return (value || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\u0111/g, 'd')
            .replace(/\u0110/g, 'D')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'D')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'D')
            .toLowerCase()
            .trim();
    }

    if (!window.kingSearchBar) {
        window.kingSearchBar = function (payload) {
            return {
                locations: (payload.locations || []).map((item) => ({
                    id: Number(item.id),
                    type: item.type || 'province',
                    name: item.name || '',
                    context: item.context || item.address || '',
                    typeLabel: item.type_label || ((payload.translations || {}).types || {})[item.type] || '',
                    normalized: '',
                })),
                translations: payload.translations || {},
                defaults: payload.defaults || {},
                activeDropdown: null,
                queries: { origin: '', destination: '' },
                origin: { id: '', type: '', name: '', context: '', typeLabel: '' },
                destination: { id: '', type: '', name: '', context: '', typeLabel: '' },
                departureDate: '',
                returnDate: '',
                showReturn: false,
                errorMessage: '',
                departurePicker: null,
                returnPicker: null,

                init() {
                    this.locations = this.locations.map((item) => {
                        item.normalized = normalize([item.name, item.context].join(' '));
                        return item;
                    });

                    this.origin = this.resolveDefault(this.defaults.origin);
                    this.destination = this.resolveDefault(this.defaults.destination);

                    if (!this.origin.id && this.locations.length > 0) {
                        this.origin = { ...this.locations[0] };
                    }

                    if (!this.destination.id && this.locations.length > 1) {
                        this.destination = { ...this.locations[1] };
                    }

                    this.departureDate = this.defaults.departure_date || this.todayDate();
                    this.returnDate = this.defaults.return_date || '';
                    this.showReturn = !!this.returnDate;

                    this.$nextTick(() => {
                        this.mountDeparturePicker();
                        if (this.showReturn) {
                            this.mountReturnPicker();
                        }
                    });

                    window.addEventListener('pageshow', () => {
                        const submitButton = this.$refs.submitButton;
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = submitButton.dataset.defaultLabel || this.translations.submit || 'Search';
                        }
                    });
                },

                todayDate() {
                    const now = new Date();
                    const day = String(now.getDate()).padStart(2, '0');
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const year = now.getFullYear();
                    return `${day}/${month}/${year}`;
                },

                normalize,

                resolveDefault(defaultValue) {
                    if (!defaultValue || !defaultValue.id || !defaultValue.type) {
                        return { id: '', type: '', name: '', context: '', typeLabel: '' };
                    }

                    const found = this.locations.find((item) => Number(item.id) === Number(defaultValue.id) && item.type === defaultValue.type);
                    if (found) {
                        return { ...found };
                    }

                    return {
                        id: Number(defaultValue.id),
                        type: defaultValue.type,
                        name: defaultValue.name || '',
                        context: defaultValue.context || '',
                        typeLabel: defaultValue.type_label || ((this.translations.types || {})[defaultValue.type] || ''),
                    };
                },

                groupedLocations(field) {
                    const query = normalize(this.queries[field]);
                    const filtered = !query
                        ? this.locations.slice(0, 24)
                        : this.locations.filter((item) => item.normalized.includes(query)).slice(0, 24);

                    const orderedTypes = ['province', 'district', 'stop'];
                    return orderedTypes
                        .map((type) => ({
                            type,
                            label: (this.translations.types || {})[type] || type,
                            items: filtered.filter((item) => item.type === type),
                        }))
                        .filter((group) => group.items.length > 0);
                },

                isSelected(field, item) {
                    const selected = this[field];
                    return Number(selected.id) === Number(item.id) && selected.type === item.type;
                },

                toggleDropdown(field) {
                    this.activeDropdown = this.activeDropdown === field ? null : field;
                    this.queries[field] = '';
                    this.errorMessage = '';

                    this.$nextTick(() => {
                        if (field === 'origin' && this.$refs.originSearch) {
                            this.$refs.originSearch.focus();
                        }
                        if (field === 'destination' && this.$refs.destinationSearch) {
                            this.$refs.destinationSearch.focus();
                        }
                    });
                },

                selectLocation(field, item) {
                    this[field] = { ...item };
                    this.activeDropdown = null;
                    this.errorMessage = '';
                },

                swapLocations() {
                    const oldOrigin = { ...this.origin };
                    this.origin = { ...this.destination };
                    this.destination = oldOrigin;
                    this.errorMessage = '';
                },

                parseDate(value) {
                    const parts = (value || '').split('/');
                    if (parts.length !== 3) {
                        return null;
                    }

                    const day = Number(parts[0]);
                    const month = Number(parts[1]) - 1;
                    const year = Number(parts[2]);
                    return new Date(year, month, day);
                },

                mountDeparturePicker() {
                    if (typeof flatpickr !== 'function' || !this.$refs.departureInput) {
                        return;
                    }

                    this.departurePicker = flatpickr(this.$refs.departureInput, {
                        dateFormat: 'd/m/Y',
                        minDate: 'today',
                        defaultDate: this.departureDate,
                        onChange: (_dates, dateStr) => {
                            this.departureDate = dateStr;
                            this.errorMessage = '';

                            if (this.returnPicker) {
                                this.returnPicker.set('minDate', dateStr);
                            }

                            if (this.returnDate) {
                                const returnDate = this.parseDate(this.returnDate);
                                const departureDate = this.parseDate(dateStr);
                                if (returnDate && departureDate && returnDate < departureDate) {
                                    this.returnDate = '';
                                }
                            }
                        },
                    });
                },

                openDeparturePicker() {
                    if (this.departurePicker) {
                        this.departurePicker.open();
                    }
                },

                mountReturnPicker() {
                    if (typeof flatpickr !== 'function' || !this.$refs.returnInput) {
                        return;
                    }

                    if (this.returnPicker) {
                        this.returnPicker.destroy();
                    }

                    this.returnPicker = flatpickr(this.$refs.returnInput, {
                        dateFormat: 'd/m/Y',
                        minDate: this.departureDate || 'today',
                        defaultDate: this.returnDate || null,
                        onChange: (_dates, dateStr) => {
                            this.returnDate = dateStr;
                            this.errorMessage = '';
                        },
                    });
                },

                enableReturnDate() {
                    this.showReturn = true;
                    this.$nextTick(() => {
                        this.mountReturnPicker();
                        this.openReturnPicker();
                    });
                },

                openReturnPicker() {
                    if (!this.showReturn) {
                        return;
                    }

                    if (!this.returnPicker) {
                        this.mountReturnPicker();
                    }

                    if (this.returnPicker) {
                        this.returnPicker.open();
                    }
                },

                removeReturnDate() {
                    this.returnDate = '';
                    this.showReturn = false;
                    if (this.returnPicker) {
                        this.returnPicker.destroy();
                        this.returnPicker = null;
                    }
                },

                submitForm() {
                    this.errorMessage = '';

                    if (!this.origin.id || !this.destination.id) {
                        this.errorMessage = this.translations.validationSelect;
                        return;
                    }

                    if (this.origin.id === this.destination.id && this.origin.type === this.destination.type) {
                        this.errorMessage = this.translations.validationDifferent;
                        return;
                    }

                    if (!this.departureDate) {
                        this.errorMessage = this.translations.datePlaceholder;
                        return;
                    }

                    const submitButton = this.$refs.submitButton;
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent = this.translations.loading;
                    }

                    this.$refs.form.submit();
                },
            };
        };
    }

    window.KingClientUI = {
        initReveals() {
            const revealItems = document.querySelectorAll('.ksb-reveal');
            if (!revealItems.length) {
                return;
            }

            if (!('IntersectionObserver' in window)) {
                revealItems.forEach((item) => item.classList.add('is-visible'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });

            revealItems.forEach((item) => observer.observe(item));
        },
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.KingClientUI.initReveals();
    });
})();
