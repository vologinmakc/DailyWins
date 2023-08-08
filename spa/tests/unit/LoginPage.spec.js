import { shallowMount } from '@vue/test-utils';
import LoginPage from '@/views/LoginPage.vue';

describe('LoginPage.vue', () => {
  it('renders login form', () => {
    const wrapper = shallowMount(LoginPage);
    expect(wrapper.find('form').exists()).toBe(true);
  });

  it('emits event on login button click', async () => {
    const wrapper = shallowMount(LoginPage);
    await wrapper.setData({ email: 'test@example.com', password: 'password123' });
    await wrapper.find('button[type="submit"]').trigger('click');
    await wrapper.vm.$nextTick();
    expect(wrapper.emitted().login).toBeTruthy();
  });
});
