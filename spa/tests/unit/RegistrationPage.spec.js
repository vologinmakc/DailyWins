import { shallowMount } from '@vue/test-utils';
import RegistrationPage from '@/views/RegistrationPage.vue';

describe('RegistrationPage.vue', () => {
  it('renders registration form', () => {
    const wrapper = shallowMount(RegistrationPage);
    expect(wrapper.find('form').exists()).toBe(true);
  });

  it('emits event on register button click', async () => {
    const wrapper = shallowMount(RegistrationPage);
    await wrapper.setData({ name: 'Test User', email: 'test@example.com', password: 'password123' });
    await wrapper.find('button[type="submit"]').trigger('click');
    await wrapper.vm.$nextTick();
    expect(wrapper.emitted().register).toBeTruthy();
  });
});
